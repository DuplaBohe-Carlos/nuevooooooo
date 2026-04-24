<?php
$filters = [
    'user_id' => $_GET['user_id'] ?? '',
    'project_id' => $_GET['project_id'] ?? '',
    'date_from' => $_GET['date_from'] ?? '',
    'date_to' => $_GET['date_to'] ?? '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'delete_timelog') {
    $id = (int)$_POST['id'];
    delete_timelog($id);
    echo '<div class="alert alert-success">✅ Registro eliminado correctamente</div>';
}

$timelogs = get_all_timelogs($filters);
?>

<div class="card" style="max-width: 100%; margin-bottom: 30px;">
    <h3>Filtros</h3>
    <form method="GET">
        <input type="hidden" name="tab" value="timelogs">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 15px;">
            <div class="form-group" style="margin:0;">
                <label>Empleado</label>
                <select name="user_id">
                    <option value="">Todos</option>
                    <?php foreach (get_all_employees() as $e): ?>
                        <option value="<?= $e['id'] ?>" <?= $filters['user_id'] == $e['id'] ? 'selected' : '' ?>><?= sanitize($e['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group" style="margin:0;">
                <label>Proyecto</label>
                <select name="project_id">
                    <option value="">Todos</option>
                    <?php foreach (get_all_projects_admin() as $p): ?>
                        <option value="<?= $p['id'] ?>" <?= $filters['project_id'] == $p['id'] ? 'selected' : '' ?>><?= sanitize($p['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group" style="margin:0;">
                <label>Fecha desde</label>
                <input type="date" name="date_from" value="<?= $filters['date_from'] ?>">
            </div>
            <div class="form-group" style="margin:0;">
                <label>Fecha hasta</label>
                <input type="date" name="date_to" value="<?= $filters['date_to'] ?>">
            </div>
        </div>
        <button type="submit" class="btn" style="width: auto; margin-top: 15px;">Filtrar</button>
        <a href="?tab=timelogs" class="btn" style="width: auto; background:#718096; margin-top: 15px;">Limpiar</a>
    </form>
</div>

<div class="card" style="max-width: 100%;">
    <h3>Registros de Tiempo (<?= count($timelogs) ?>)</h3>
    <table style="width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 14px;">
        <thead>
            <tr style="border-bottom: 2px solid #e2e8f0;">
                <th style="text-align: left; padding: 8px;">Fecha</th>
                <th style="text-align: left; padding: 8px;">Empleado</th>
                <th style="text-align: left; padding: 8px;">Proyecto</th>
                <th style="text-align: center; padding: 8px;">Entrada</th>
                <th style="text-align: center; padding: 8px;">Salida</th>
                <th style="text-align: center; padding: 8px;">Horas</th>
                <th style="text-align: center; padding: 8px;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($timelogs as $log): ?>
            <tr style="border-bottom: 1px solid #edf2f7;">
                <td style="padding: 8px;"><?= date('d/m/Y', strtotime($log['clock_in'])) ?></td>
                <td style="padding: 8px;"><?= sanitize($log['user_name']) ?></td>
                <td style="padding: 8px;"><?= $log['project_name'] ? sanitize($log['project_name']) : '-' ?></td>
                <td style="padding: 8px; text-align: center;"><?= date('H:i', strtotime($log['clock_in'])) ?></td>
                <td style="padding: 8px; text-align: center;"><?= $log['clock_out'] ? date('H:i', strtotime($log['clock_out'])) : '<span style="color:#e53e3e;">En curso</span>' ?></td>
                <td style="padding: 8px; text-align: center;">
                    <?php if ($log['clock_out']): ?>
                        <?= round((strtotime($log['clock_out']) - strtotime($log['clock_in'])) / 3600, 2) ?>h
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
                <td style="padding: 8px; text-align: center;">
                    <form method="POST" style="display: inline;" onsubmit="return confirmDelete()">
                        <input type="hidden" name="action" value="delete_timelog">
                        <input type="hidden" name="id" value="<?= $log['id'] ?>">
                        <button type="submit" style="background: none; border: none; color:#e53e3e; cursor: pointer;">🗑️ Borrar</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>