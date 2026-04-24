<?php
require_once __DIR__ . '/../config/init.php';

function get_active_log($user_id) {
    $database = new Database();
    $db = $database->getConnection();

    $stmt = $db->prepare("SELECT * FROM time_logs WHERE user_id = ? AND clock_out IS NULL ORDER BY clock_in DESC LIMIT 1");
    $stmt->execute([$user_id]);
    return $stmt->fetch();
}

function clock_in($user_id, $project_id) {
    $database = new Database();
    $db = $database->getConnection();

    // Verificar que no haya fichaje abierto
    if (get_active_log($user_id)) {
        return ['success' => false, 'message' => 'Ya tienes un fichaje abierto. Debes fichar salida primero.'];
    }

    $stmt = $db->prepare("INSERT INTO time_logs (user_id, project_id, clock_in) VALUES (?, ?, NOW())");
    $stmt->execute([$user_id, $project_id ?: null]);

    return ['success' => true, 'log_id' => $db->lastInsertId()];
}

function clock_out($user_id, $notes = '') {
    $database = new Database();
    $db = $database->getConnection();

    $active_log = get_active_log($user_id);
    if (!$active_log) {
        return ['success' => false, 'message' => 'No tienes ningún fichaje abierto.'];
    }

    $stmt = $db->prepare("UPDATE time_logs SET clock_out = NOW(), notes = ? WHERE id = ?");
    $stmt->execute([$notes, $active_log['id']]);

    return ['success' => true];
}

function get_today_logs($user_id) {
    $database = new Database();
    $db = $database->getConnection();

    $stmt = $db->prepare("
        SELECT tl.*, p.name as project_name 
        FROM time_logs tl 
        LEFT JOIN projects p ON tl.project_id = p.id 
        WHERE tl.user_id = ? AND DATE(tl.clock_in) = CURDATE() 
        ORDER BY tl.clock_in DESC
    ");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll();
}

function get_total_today_hours($user_id) {
    $logs = get_today_logs($user_id);
    $total = 0;

    foreach ($logs as $log) {
        if ($log['clock_out']) {
            $in = new DateTime($log['clock_in']);
            $out = new DateTime($log['clock_out']);
            $diff = $in->diff($out);
            $total += $diff->h + ($diff->i / 60);
        }
    }

    return number_format($total, 2);
}

function get_all_projects() {
    $database = new Database();
    $db = $database->getConnection();

    $stmt = $db->prepare("SELECT id, name FROM projects ORDER BY name");
    $stmt->execute();
    return $stmt->fetchAll();
}

function format_time($datetime) {
    return date('H:i', strtotime($datetime));
}