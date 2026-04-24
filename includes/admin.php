<?php
require_once __DIR__ . '/../config/init.php';

// -------------------- GESTION EMPLEADOS
function get_all_employees() {
    $db = (new Database())->getConnection();
    $stmt = $db->prepare("SELECT id, name, email, role, created_at FROM users ORDER BY name");
    $stmt->execute();
    return $stmt->fetchAll();
}

function get_employee($id) {
    $db = (new Database())->getConnection();
    $stmt = $db->prepare("SELECT id, name, email, role FROM users WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function create_employee($name, $email, $password, $role) {
    $db = (new Database())->getConnection();
    
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->rowCount() > 0) return ['success' => false, 'message' => 'Email ya existe'];

    $hash = password_hash($password, PASSWORD_DEFAULT, ['cost' => 12]);
    $stmt = $db->prepare("INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)");
    
    try {
        $stmt->execute([$name, $email, $hash, $role]);
        return ['success' => true, 'id' => $db->lastInsertId()];
    } catch (Exception $e) {
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

function update_employee($id, $name, $email, $role, $password = null) {
    $db = (new Database())->getConnection();
    
    if ($password) {
        $hash = password_hash($password, PASSWORD_DEFAULT, ['cost' => 12]);
        $stmt = $db->prepare("UPDATE users SET name = ?, email = ?, role = ?, password_hash = ? WHERE id = ?");
        $stmt->execute([$name, $email, $role, $hash, $id]);
    } else {
        $stmt = $db->prepare("UPDATE users SET name = ?, email = ?, role = ? WHERE id = ?");
        $stmt->execute([$name, $email, $role, $id]);
    }
    
    return ['success' => true];
}

function delete_employee($id) {
    $db = (new Database())->getConnection();
    $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$id]);
    return true;
}

// -------------------- GESTION PROYECTOS
function get_all_projects_admin() {
    $db = (new Database())->getConnection();
    $stmt = $db->prepare("SELECT id, name, client, budgeted_hours, created_at FROM projects ORDER BY name");
    $stmt->execute();
    return $stmt->fetchAll();
}

function get_project($id) {
    $db = (new Database())->getConnection();
    $stmt = $db->prepare("SELECT id, name, client, budgeted_hours FROM projects WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function create_project($name, $client, $budgeted_hours) {
    $db = (new Database())->getConnection();
    $stmt = $db->prepare("INSERT INTO projects (name, client, budgeted_hours) VALUES (?, ?, ?)");
    $stmt->execute([$name, $client, $budgeted_hours]);
    return ['success' => true, 'id' => $db->lastInsertId()];
}

function update_project($id, $name, $client, $budgeted_hours) {
    $db = (new Database())->getConnection();
    $stmt = $db->prepare("UPDATE projects SET name = ?, client = ?, budgeted_hours = ? WHERE id = ?");
    $stmt->execute([$name, $client, $budgeted_hours, $id]);
    return ['success' => true];
}

function delete_project($id) {
    $db = (new Database())->getConnection();
    $stmt = $db->prepare("DELETE FROM projects WHERE id = ?");
    $stmt->execute([$id]);
    return true;
}

// -------------------- GESTION REGISTROS DE TIEMPO
function get_all_timelogs($filters = []) {
    $db = (new Database())->getConnection();
    
    $sql = "SELECT tl.*, u.name as user_name, p.name as project_name 
            FROM time_logs tl 
            JOIN users u ON tl.user_id = u.id 
            LEFT JOIN projects p ON tl.project_id = p.id 
            WHERE 1=1";
    
    $params = [];
    
    if (!empty($filters['user_id'])) {
        $sql .= " AND tl.user_id = ?";
        $params[] = $filters['user_id'];
    }
    if (!empty($filters['project_id'])) {
        $sql .= " AND tl.project_id = ?";
        $params[] = $filters['project_id'];
    }
    if (!empty($filters['date_from'])) {
        $sql .= " AND DATE(tl.clock_in) >= ?";
        $params[] = $filters['date_from'];
    }
    if (!empty($filters['date_to'])) {
        $sql .= " AND DATE(tl.clock_in) <= ?";
        $params[] = $filters['date_to'];
    }
    
    $sql .= " ORDER BY tl.clock_in DESC LIMIT 500";
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function delete_timelog($id) {
    $db = (new Database())->getConnection();
    $stmt = $db->prepare("DELETE FROM time_logs WHERE id = ?");
    $stmt->execute([$id]);
    return true;
}