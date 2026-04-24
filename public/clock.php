<?php
require_once __DIR__ . '/../includes/time.php';
require_auth();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = sanitize($_POST['action']);
    $user_id = $_SESSION['user_id'];

    if ($action === 'in') {
        $project_id = isset($_POST['project_id']) ? (int)$_POST['project_id'] : null;
        $result = clock_in($user_id, $project_id);
    } elseif ($action === 'out') {
        $notes = sanitize($_POST['notes'] ?? '');
        $result = clock_out($user_id, $notes);
    } else {
        $result = ['success' => false, 'message' => 'Acción no válida'];
    }

    if ($result['success']) {
        $success = $action === 'in' ? '✅ Fichaje de entrada registrado correctamente' : '✅ Fichaje de salida registrado correctamente';
    } else {
        $error = $result['message'];
    }
}

redirect('dashboard.php');
?>