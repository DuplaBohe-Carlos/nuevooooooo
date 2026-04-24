<?php
require_once __DIR__ . '/../includes/auth.php';

if (is_logged_in()) {
    redirect('dashboard.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];

    if (!validate_email($email)) {
        $error = 'Por favor introduce un correo electrónico válido';
    } elseif (empty($password)) {
        $error = 'Por favor introduce tu contraseña';
    } else {
        $result = login_user($email, $password);
        
        if ($result['success']) {
            redirect('dashboard.php');
        } else {
            $error = $result['message'];
        }
    }
}

include __DIR__ . '/../includes/header.php';
?>

<div class="container">
    <div class="card">
        <h2>Iniciar Sesión</h2>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        
        <?php if (isset($_GET['registered'])): ?>
            <div class="alert alert-success">Cuenta creada correctamente. Ahora puedes iniciar sesión.</div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Correo Electrónico</label>
                <input type="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label>Contraseña</label>
                <input type="password" name="password" required>
            </div>
            
            <button type="submit" class="btn">Ingresar</button>
        </form>
        
        <div class="link">
            ¿No tienes cuenta? <a href="register.php">Registrarse</a>
        </div>
    </div>
</div>

</body>
</html>