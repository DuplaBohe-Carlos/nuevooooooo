<div style="text-align: right; margin-bottom: 20px;">
    <a href="?tab=projects&action=create" class="btn" style="width: auto; background:#38a169;">➕ Nuevo Proyecto</a>
</div>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['action'] === 'create') {
        $name = sanitize($_POST['name']);
        $client = sanitize($_POST['client']);
        $hours = (float)$_POST['budgeted_hours'];
        
        create_project($name, $client, $hours);
        echo '<div class="alert alert-success">✅ Proyecto creado correctamente</div>';
    }
    
    if ($_POST['action'] === 'update') {
        $id = (int)$_POST['id'];
        $name = sanitize($_POST['name']);
        $client = sanitize($_POST['client']);
        $hours = (float)$_POST['budgeted_hours'];
        
        update_project($id, $name, $client, $hours);
        echo '<div class="alert alert-success">✅ Proyecto actualizado correctamente</div>';
    }
    
    if ($_POST['action'] === 'delete') {
        $id = (int)$_POST['id'];
        delete_project($id);
        echo '<div class="alert alert-success">✅ Proyecto eliminado correctamente</div>';
    }
}

if ($action === 'create' || $action === 'edit') {
    $proj = $action === 'edit' ? get_project((int)$_GET['id']) : ['name'=>'', 'client'=>'', 'budgeted_hours'=>''];
?>
<div class="card" style="max-width: 500px; margin: 0 auto 30px;">
    <h3><?= $action === 'create' ? 'Nuevo Proyecto' : 'Editar Proyecto' ?></h3>
    <form method="POST">
        <input type="hidden" name="action" value="<?= $action === 'create' ? 'create' : 'update' ?>">
        <?php if ($action === 'edit'): ?>
            <input type="hidden" name="id" value="<?= $proj['id'] ?>">
        <?php endif; ?>
        
        <div class="form-group">
            <label>Nombre del Proyecto</label>
            <input type="text" name="name" value="<?= sanitize($proj['name']) ?>" required>
        </div>
        
        <div class="form-group">
            <label>Cliente</label>
            <input type="text" name="client" value="<?= sanitize($proj['client']) ?>" required>
        </div>
        
        <div class="form-group">
            <label>Horas Presupuestadas</label>
            <input type="number" step="0.5" name="budgeted_hours" value="<?= $proj['budgeted_hours'] ?>" required>
        </div>
        
        <button type="submit" class="btn">Guardar</button>
        <a href="?tab=projects" class="btn" style="width: auto; background:#718096; margin-top:10px;">Cancelar</a>
    </form>
</div>
<?php
}

$projects = get_all_projects_admin();
?>

<div class="card" style="max-width: 100%;">
    <h3>Listado de Proyectos (<?= count($projects) ?>)</h3>
    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
        <thead>
            <tr style="border-bottom: 2px solid #e2e8f0;">
                <th style="text-align: left; padding: 12px;">Proyecto</th>
                <th style="text-align: left; padding: 12px;">Cliente</th>
                <th style="text-align: right; padding: 12px;">Horas Presup.</th>
                <th style="text-align: center; padding: 12px;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($projects as $p): ?>
            <tr style="border-bottom: 1px solid #edf2f7;">
                <td style="padding: 12px;"><strong><?= sanitize($p['name']) ?></strong></td>
                <td style="padding: 12px;"><?= sanitize($p['client']) ?></td>
                <td style="padding: 12px; text-align: right;"><?= $p['budgeted_hours'] ?>h</td>
                <td style="padding: 12px; text-align: center;">
                    <a href="?tab=projects&action=edit&id=<?= $p['id'] ?>" style="color:#3182ce; margin:0 8px;">✏️ Editar</a>
                    <form method="POST" style="display: inline;" onsubmit="return confirmDelete()">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= $p['id'] ?>">
                        <button type="submit" style="background: none; border: none; color:#e53e3e; cursor: pointer;">🗑️ Borrar</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>