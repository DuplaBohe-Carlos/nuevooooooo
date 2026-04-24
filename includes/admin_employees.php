<div style="text-align: right; margin-bottom: 20px;">
    <a href="?tab=employees&action=create" class="btn" style="width: auto; background:#38a169;">➕ Nuevo Empleado</a>
</div>

<?php
// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['action'] === 'create') {
        $name = sanitize($_POST['name']);
        $email = sanitize($_POST['email']);
        $password = $_POST['password'];
        $role = sanitize($_POST['role']);
        
        $result = create_employee($name, $email, $password, $role);
        if ($result['success']) {
            echo '<div class="alert alert-success">✅ Empleado creado correctamente</div>';
        } else {
            echo '<div class="alert alert-danger">❌ '.$result['message'].'</div>';
        }
    }
    
    if ($_POST['action'] === 'update') {
        $id = (int)$_POST['id'];
        $name = sanitize($_POST['name']);
        $email = sanitize($_POST['email']);
        $role = sanitize($_POST['role']);
        $password = !empty($_POST['password']) ? $_POST['password'] : null;
        
        update_employee($id, $name, $email, $role, $password);
        echo '<div class="alert alert-success">✅ Empleado actualizado correctamente</div>';
    }
    
    if ($_POST['action'] === 'delete') {
        $id = (int)$_POST['id'];
        delete_employee($id);
        echo '<div class="alert alert-success">✅ Empleado eliminado correctamente</div>';
    }
}

if ($action === 'create' || $action === 'edit') {
    $emp = $action === 'edit' ? get_employee((int)$_GET['id']) : ['name'=>'', 'email'=>'', 'role'=>'empleado'];
?>
<div class="card" style="max-width: 500px; margin: 0 auto 30px;">
    <h3><?= $action === 'create' ? 'Nuevo Empleado' : 'Editar Empleado' ?></h3>
    <form method="POST">
        <input type="hidden" name="action" value="<?= $action === 'create' ? 'create' : 'update' ?>">
        <?php if ($action === 'edit'): ?>
            <input type="hidden" name="id" value="<?= $emp['id'] ?>">
        <?php endif; ?>
        
        <div class="form-group">
            <label>Nombre</label>
            <input type="text" name="name" value="<?= sanitize($emp['name']) ?>" required>
        </div>
        
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" value="<?= sanitize($emp['email']) ?>" required>
        </div>
        
        <div class="form-group">
            <label>Contraseña <?= $action === 'edit' ? '(dejar vacío para mantener)' : '' ?></label>
            <input type="password" name="password">
        </div>
        
        <div class="form-group">
            <label>Rol</label>
            <select name="role" required>
                <option value="empleado" <?= $emp['role'] === 'empleado' ? 'selected' : '' ?>>Empleado</option>
                <option value="admin" <?= $emp['role'] === 'admin' ? 'selected' : '' ?>>Administrador</option>
            </select>
        </div>
        
        <button type="submit" class="btn">Guardar</button>
        <a href="?tab=employees" class="btn" style="width: auto; background:#718096; margin-top:10px;">Cancelar</a>
    </form>
</div>
<?php
}

// Listado
$employees = get_all_employees();
?>

<div class="card" style="max-width: 100%;">
    <h3>Listado de Empleados (<?= count($employees) ?>)</h3>
    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
        <thead>
            <tr style="border-bottom: 2px solid #e2e8f0;">
                <th style="text-align: left; padding: 12px;">Nombre</th>
                <th style="text-align: left; padding: 12px;">Email</th>
                <th style="text-align: left; padding: 12px;">Rol</th>
                <th style="text-align: center; padding: 12px;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($employees as $e): ?>
            <tr style="border-bottom: 1px solid #edf2f7;">
                <td style="padding: 12px;"><strong><?= sanitize($e['name']) ?></strong></td>
                <td style="padding: 12px;"><?= sanitize($e['email']) ?></td>
                <td style="padding: 12px;"><?= ucfirst($e['role']) ?></td>
                <td style="padding: 12px; text-align: center;">
                    <a href="?tab=employees&action=edit&id=<?= $e['id'] ?>" style="color:#3182ce; margin:0 8px;">✏️ Editar</a>
                    <form method="POST" style="display: inline;" onsubmit="return confirmDelete()">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= $e['id'] ?>">
                        <button type="submit" style="background: none; border: none; color:#e53e3e; cursor: pointer;">🗑️ Borrar</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>