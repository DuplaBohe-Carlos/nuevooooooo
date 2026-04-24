<?php
require_once __DIR__ . '/../includes/auth.php';

if (is_logged_in()) {
    redirect('dashboard.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = sanitize($_POST['role']);

    if (empty($name) || strlen($name) < 2) {
        $error = 'Por favor introduce un nombre válido';
    } elseif (!validate_email($email)) {
        $error = 'Por favor introduce un correo electrónico válido';
    } elseif (strlen($password) < 6) {
        $error = 'La contraseña debe tener al menos 6 caracteres';
    } elseif ($password !== $confirm_password) {
        $error = 'Las contraseñas no coinciden';
    } elseif (!in_array($role, ['admin', 'empleado'])) {
        $error = 'Rol no válido';
    } else {
        $result = register_user($name, $email, $password, $role);
        
        if ($result['success']) {
            redirect('login.php?registered=1');
        } else {
            $error = $result['message'];
        }
    }
}

include __DIR__ . '/../includes/header.php';
?>

<div class="container">
    <div class="card">
        <h2>Crear Cuenta</h2>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Nombre Completo</label>
                <input type="text" name="name" required>
            </div>
            
            <div class="form-group">
                <label>Correo Electrónico</label>
                <input type="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label>Contraseña</label>
                <input type="password" name="password" required>
            </div>
            
            <div class="form-group">
                <label>Confirmar Contraseña</label>
                <input type="password" name="confirm_password" required>
            </div>
            
            <div class="form-group">
                <label>Tipo de Usuario</label>
                <select name="role" required>
                    <option value="empleado">Empleado</option>
                    <option value="admin">Administrador</option>
                </select>
            </div>
            
            <button type="submit" class="btn">Registrarse</button>
        </form>
        
        <div class="link">
            ¿Ya tienes cuenta? <a href="login.php">Iniciar Sesión</a>
        </div>
    </div>
</div>

</body>
</html>