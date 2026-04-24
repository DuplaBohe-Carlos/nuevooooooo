<?php
require_once __DIR__ . '/../config/init.php';

function register_user($name, $email, $password, $role = 'empleado') {
    $database = new Database();
    $db = $database->getConnection();

    // Verificar si el email ya existe
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    
    if ($stmt->rowCount() > 0) {
        return ['success' => false, 'message' => 'Este correo electrónico ya está registrado'];
    }

    // Cifrar contraseña
    $password_hash = password_hash($password, PASSWORD_DEFAULT, ['cost' => 12]);

    // Insertar usuario
    $stmt = $db->prepare("INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)");
    
    try {
        $stmt->execute([$name, $email, $password_hash, $role]);
        return ['success' => true, 'user_id' => $db->lastInsertId()];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Error al crear el usuario: ' . $e->getMessage()];
    }
}

function login_user($email, $password) {
    $database = new Database();
    $db = $database->getConnection();

    $stmt = $db->prepare("SELECT id, name, password_hash, role FROM users WHERE email = ?");
    $stmt->execute([$email]);
    
    if ($stmt->rowCount() === 0) {
        return ['success' => false, 'message' => 'Correo o contraseña incorrectos'];
    }

    $user = $stmt->fetch();

    if (!password_verify($password, $user['password_hash'])) {
        return ['success' => false, 'message' => 'Correo o contraseña incorrectos'];
    }

    // Regenerar ID de sesión para seguridad
    session_regenerate_id(true);

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_email'] = $email;
    $_SESSION['user_role'] = $user['role'];

    return ['success' => true, 'user' => $user];
}

function logout_user() {
    $_SESSION = [];
    session_unset();
    session_destroy();
    
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    return true;
}