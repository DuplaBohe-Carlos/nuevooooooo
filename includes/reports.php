<?php
require_once __DIR__ . '/../config/init.php';

function get_all_projects_with_hours() {
    $database = new Database();
    $db = $database->getConnection();

    $stmt = $db->prepare("
        SELECT 
            p.id,
            p.name,
            p.client,
            p.budgeted_hours,
            SUM(TIMESTAMPDIFF(MINUTE, tl.clock_in, tl.clock_out) / 60) as actual_hours
        FROM projects p
        LEFT JOIN time_logs tl ON p.id = tl.project_id AND tl.clock_out IS NOT NULL
        GROUP BY p.id, p.name, p.client, p.budgeted_hours
        ORDER BY p.name
    ");
    $stmt->execute();
    $projects = $stmt->fetchAll();

    foreach ($projects as &$project) {
        $project['actual_hours'] = $project['actual_hours'] ? round($project['actual_hours'], 2) : 0;
        $project['percentage'] = $project['budgeted_hours'] > 0 
            ? round(($project['actual_hours'] / $project['budgeted_hours']) * 100, 1) 
            : 0;
        
        if ($project['percentage'] >= 100) {
            $project['status'] = 'danger';
        } elseif ($project['percentage'] >= 95) {
            $project['status'] = 'warning';
        } elseif ($project['percentage'] >= 70) {
            $project['status'] = 'caution';
        } else {
            $project['status'] = 'ok';
        }
    }

    return $projects;
}

function get_hours_by_employee() {
    $database = new Database();
    $db = $database->getConnection();

    $stmt = $db->prepare("
        SELECT 
            u.name,
            SUM(TIMESTAMPDIFF(MINUTE, tl.clock_in, tl.clock_out) / 60) as total_hours
        FROM users u
        LEFT JOIN time_logs tl ON u.id = tl.user_id AND tl.clock_out IS NOT NULL
        WHERE u.role = 'empleado'
        GROUP BY u.id, u.name
        HAVING total_hours > 0
        ORDER BY total_hours DESC
    ");
    $stmt->execute();
    return $stmt->fetchAll();
}

function get_total_statistics() {
    $database = new Database();
    $db = $database->getConnection();

    $stats = [];

    $stmt = $db->prepare("SELECT SUM(budgeted_hours) as total_budgeted FROM projects");
    $stmt->execute();
    $stats['total_budgeted'] = $stmt->fetchColumn() ?: 0;

    $stmt = $db->prepare("SELECT SUM(TIMESTAMPDIFF(MINUTE, clock_in, clock_out) / 60) as total_actual FROM time_logs WHERE clock_out IS NOT NULL");
    $stmt->execute();
    $stats['total_actual'] = $stmt->fetchColumn() ?: 0;

    $stmt = $db->prepare("SELECT COUNT(*) as total_projects FROM projects");
    $stmt->execute();
    $stats['total_projects'] = $stmt->fetchColumn();

    $stmt = $db->prepare("
        SELECT COUNT(*) as overbudget FROM (
            SELECT p.id, p.budgeted_hours, SUM(TIMESTAMPDIFF(MINUTE, tl.clock_in, tl.clock_out) / 60) as actual
            FROM projects p
            LEFT JOIN time_logs tl ON p.id = tl.project_id AND tl.clock_out IS NOT NULL
            GROUP BY p.id, p.budgeted_hours
        ) as subquery
        WHERE actual > budgeted_hours
    ");
    $stmt->execute();
    $stats['overbudget_projects'] = $stmt->fetchColumn() ?: 0;

    return $stats;
}