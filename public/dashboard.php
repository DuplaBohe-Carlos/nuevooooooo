<?php
require_once __DIR__ . '/../config/init.php';
require_once __DIR__ . '/../includes/time.php';
require_auth();

$user_id = $_SESSION['user_id'];
$active_log = get_active_log($user_id);
$today_logs = get_today_logs($user_id);
$total_hours = get_total_today_hours($user_id);
$projects = get_all_projects();

include __DIR__ . '/../includes/header.php';
?>

<div class="navbar">
    <div class="container">
        <div><strong>Sistema de Horas</strong></div>
        <div>
            <span>Hola, <?= sanitize($_SESSION['user_name']) ?> (<?= $_SESSION['user_role'] ?>)</span>
            <?php if (is_admin()): ?>
                <a href="reports.php">📊 Informes</a>
            <?php endif; ?>
            <a href="logout.php">Cerrar Sesión</a>
        </div>
    </div>
</div>

<div class="container">
    <div class="card">
        <h2>Control de Horas</h2>
        
        <div style="text-align: center; margin-bottom: 30px;">
            <h3 style="color: #2d3748; font-size: 48px; font-weight: 300;"><?= date('H:i') ?></h3>
            <p style="color: #718096;"><?= date('d/m/Y') ?></p>
            
            <div style="margin-top: 20px; padding: 12px; background: #ebf8ff; border-radius: 8px;">
                <strong>Total hoy: <?= $total_hours ?> horas</strong>
            </div>
        </div>

        <?php if (!$active_log): ?>
            <form method="POST" action="clock.php">
                <input type="hidden" name="action" value="in">
                
                <div class="form-group">
                    <label>Selecciona Proyecto</label>
                    <select name="project_id">
                        <option value="">Sin proyecto</option>
                        <?php foreach ($projects as $project): ?>
                            <option value="<?= $project['id'] ?>"><?= sanitize($project['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <button type="submit" class="btn" style="background: #38a169; font-size: 20px; padding: 20px;">
                    🟢 FICHAR ENTRADA
                </button>
            </form>
        <?php else: ?>
            <form method="POST" action="clock.php">
                <input type="hidden" name="action" value="out">
                
                <div style="text-align: center; margin-bottom: 20px; padding: 15px; background: #fff3cd; border-radius: 8px;">
                    ⏳ Actualmente fichado desde las <strong><?= format_time($active_log['clock_in']) ?></strong>
                </div>
                
                <div class="form-group">
                    <label>Notas (opcional)</label>
                    <input type="text" name="notes" placeholder="Tareas realizadas...">
                </div>
                
                <button type="submit" class="btn" style="background: #e53e3e; font-size: 20px; padding: 20px;">
                    🔴 FICHAR SALIDA
                </button>
            </form>
        <?php endif; ?>
    </div>

    <?php if (!empty($today_logs)): ?>
    <div class="card" style="max-width: 600px;">
        <h3>Historial de hoy</h3>
        
        <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
            <thead>
                <tr style="border-bottom: 2px solid #e2e8f0;">
                    <th style="text-align: left; padding: 12px 8px;">Entrada</th>
                    <th style="text-align: left; padding: 12px 8px;">Salida</th>
                    <th style="text-align: left; padding: 12px 8px;">Proyecto</th>
                    <th style="text-align: right; padding: 12px 8px;">Horas</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($today_logs as $log): ?>
                <tr style="border-bottom: 1px solid #edf2f7;">
                    <td style="padding: 12px 8px;"><?= format_time($log['clock_in']) ?></td>
                    <td style="padding: 12px 8px;"><?= $log['clock_out'] ? format_time($log['clock_out']) : '<span style="color: #e53e3e;">En curso</span>' ?></td>
                    <td style="padding: 12px 8px;"><?= $log['project_name'] ? sanitize($log['project_name']) : '-' ?></td>
                    <td style="padding: 12px 8px; text-align: right;">
                        <?php 
                        if ($log['clock_out']) {
                            $in = new DateTime($log['clock_in']);
                            $out = new DateTime($log['clock_out']);
                            $diff = $in->diff($out);
                            echo sprintf('%02d:%02d', $diff->h, $diff->i);
                        } else {
                            echo '-';
                        }
                        ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

</body>
</html>