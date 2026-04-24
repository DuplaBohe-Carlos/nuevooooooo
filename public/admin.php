<?php
require_once __DIR__ . '/../config/init.php';
require_once __DIR__ . '/../includes/admin.php';

require_admin();

$tab = $_GET['tab'] ?? 'employees';
$action = $_GET['action'] ?? 'list';

include __DIR__ . '/../includes/header.php';
?>

<div class="navbar">
    <div class="container">
        <div><strong>🔧 Panel de Administración</strong></div>
        <div>
            <a href="dashboard.php">Panel Principal</a>
            <a href="reports.php">Informes</a>
            <a href="logout.php">Cerrar Sesión</a>
        </div>
    </div>
</div>

<div class="container">
    <h1>Panel de Administración</h1>
    
    <!-- Tabs -->
    <div style="display: flex; gap: 10px; margin-bottom: 30px;">
        <a href="?tab=employees" class="btn" style="width: auto; <?= $tab === 'employees' ? 'background:#2b6cb0;' : '' ?>">👥 Empleados</a>
        <a href="?tab=projects" class="btn" style="width: auto; <?= $tab === 'projects' ? 'background:#2b6cb0;' : '' ?>">📋 Proyectos</a>
        <a href="?tab=timelogs" class="btn" style="width: auto; <?= $tab === 'timelogs' ? 'background:#2b6cb0;' : '' ?>">⏱️ Registros</a>
    </div>

    <?php
    if ($tab === 'employees') include __DIR__ . '/../includes/admin_employees.php';
    if ($tab === 'projects') include __DIR__ . '/../includes/admin_projects.php';
    if ($tab === 'timelogs') include __DIR__ . '/../includes/admin_timelogs.php';
    ?>

</div>

<script>
function confirmDelete() {
    return confirm('⚠️ ¿Estás seguro que quieres eliminar este elemento? Esta acción no se puede deshacer.');
}
</script>

</body>
</html>