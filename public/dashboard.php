<?php
require_once __DIR__ . '/../config/init.php';
require_auth();
include __DIR__ . '/../includes/header.php';
?>

<div class="navbar">
    <div class="container">
        <div><strong>Sistema de Horas</strong></div>
        <div>
            <span>Hola, <?= sanitize($_SESSION['user_name']) ?> (<?= $_SESSION['user_role'] ?>)</span>
            <a href="logout.php">Cerrar Sesión</a>
        </div>
    </div>
</div>

<div class="container">
    <div class="card">
        <h2>Bienvenido al Sistema</h2>
        <p style="text-align: center; color: #718096; margin-bottom: 20px;">
            ✅ Has iniciado sesión correctamente
        </p>
        
        <div style="background: #f7fafc; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
            <p><strong>Usuario ID:</strong> <?= $_SESSION['user_id'] ?></p>
            <p><strong>Email:</strong> <?= sanitize($_SESSION['user_email']) ?></p>
            <p><strong>Rol:</strong> <?= ucfirst($_SESSION['user_role']) ?></p>
        </div>

        <div style="text-align: center; color: #a0aec0;">
            Iteración 1 completada correctamente<br>
            Próximamente: Control de Horas
        </div>
    </div>
</div>

</body>
</html>