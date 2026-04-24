<?php
require_once __DIR__ . '/../config/init.php';
require_once __DIR__ . '/../includes/reports.php';

require_admin();

$projects = get_all_projects_with_hours();
$employees = get_hours_by_employee();
$stats = get_total_statistics();

include __DIR__ . '/../includes/header.php';
?>

<div class="navbar">
    <div class="container">
        <div><strong>Sistema de Horas - Informes</strong></div>
        <div>
            <a href="dashboard.php">Panel Principal</a>
            <a href="logout.php">Cerrar Sesión</a>
        </div>
    </div>
</div>

<div class="container">
    <h1>Informe de Proyectos</h1>

    <!-- Tarjetas estadísticas -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <div class="card" style="margin: 0;">
            <h3 style="color: #718096; font-size: 14px; margin-bottom: 10px;">Total Proyectos</h3>
            <div style="font-size: 32px; font-weight: 700;"><?= $stats['total_projects'] ?></div>
        </div>
        <div class="card" style="margin: 0;">
            <h3 style="color: #718096; font-size: 14px; margin-bottom: 10px;">Horas Presupuestadas</h3>
            <div style="font-size: 32px; font-weight: 700;"><?= round($stats['total_budgeted']) ?></div>
        </div>
        <div class="card" style="margin: 0;">
            <h3 style="color: #718096; font-size: 14px; margin-bottom: 10px;">Horas Reales</h3>
            <div style="font-size: 32px; font-weight: 700;"><?= round($stats['total_actual']) ?></div>
        </div>
        <div class="card" style="margin: 0; <?= $stats['overbudget_projects'] > 0 ? 'border: 2px solid #e53e3e;' : '' ?>">
            <h3 style="color: #718096; font-size: 14px; margin-bottom: 10px;">Proyectos Sobrepasados</h3>
            <div style="font-size: 32px; font-weight: 700; color: #e53e3e;"><?= $stats['overbudget_projects'] ?></div>
        </div>
    </div>

    <!-- Tabla de proyectos -->
    <div class="card" style="max-width: 100%; margin-bottom: 30px;">
        <h3>Resumen de Proyectos</h3>
        <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
            <thead>
                <tr style="border-bottom: 2px solid #e2e8f0;">
                    <th style="text-align: left; padding: 12px;">Proyecto</th>
                    <th style="text-align: left; padding: 12px;">Cliente</th>
                    <th style="text-align: right; padding: 12px;">Presupuesto</th>
                    <th style="text-align: right; padding: 12px;">Real</th>
                    <th style="text-align: right; padding: 12px;">%</th>
                    <th style="text-align: center; padding: 12px;">Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($projects as $p): ?>
                <tr style="border-bottom: 1px solid #edf2f7;">
                    <td style="padding: 12px;"><strong><?= sanitize($p['name']) ?></strong></td>
                    <td style="padding: 12px;"><?= sanitize($p['client']) ?></td>
                    <td style="padding: 12px; text-align: right;"><?= $p['budgeted_hours'] ?>h</td>
                    <td style="padding: 12px; text-align: right;"><?= $p['actual_hours'] ?>h</td>
                    <td style="padding: 12px; text-align: right;"><?= $p['percentage'] ?>%</td>
                    <td style="padding: 12px; text-align: center;">
                        <?php if ($p['status'] === 'danger'): ?>
                            <span style="background: #fed7d7; color: #c53030; padding: 4px 10px; border-radius: 20px; font-size: 12px;">❌ SOBREPASADO</span>
                        <?php elseif ($p['status'] === 'warning'): ?>
                            <span style="background: #fff3cd; color: #d69e2e; padding: 4px 10px; border-radius: 20px; font-size: 12px;">⚠️ EN RIESGO</span>
                        <?php elseif ($p['status'] === 'caution'): ?>
                            <span style="background: #fef5e7; color: #dd6b20; padding: 4px 10px; border-radius: 20px; font-size: 12px;">🟡 EN CURSO</span>
                        <?php else: ?>
                            <span style="background: #c6f6d5; color: #38a169; padding: 4px 10px; border-radius: 20px; font-size: 12px;">✅ OK</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Gráficos -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        <div class="card" style="max-width: 100%; margin: 0;">
            <h3 style="margin-bottom: 20px;">Horas Presupuestadas vs Reales</h3>
            <canvas id="barChart"></canvas>
        </div>
        
        <div class="card" style="max-width: 100%; margin: 0;">
            <h3 style="margin-bottom: 20px;">Horas por Empleado</h3>
            <canvas id="pieChart"></canvas>
        </div>
    </div>
</div>

<script>
// Gráfico de barras
const barCtx = document.getElementById('barChart').getContext('2d');
new Chart(barCtx, {
    type: 'bar',
    data: {
        labels: <?= json_encode(array_column($projects, 'name')) ?>,
        datasets: [
            {
                label: 'Presupuestadas',
                data: <?= json_encode(array_column($projects, 'budgeted_hours')) ?>,
                backgroundColor: '#3182ce',
            },
            {
                label: 'Reales',
                data: <?= json_encode(array_column($projects, 'actual_hours')) ?>,
                backgroundColor: '#38a169',
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'top' }
        }
    }
});

// Gráfico circular
const pieCtx = document.getElementById('pieChart').getContext('2d');
new Chart(pieCtx, {
    type: 'doughnut',
    data: {
        labels: <?= json_encode(array_column($employees, 'name')) ?>,
        datasets: [{
            data: <?= json_encode(array_column($employees, 'total_hours')) ?>,
            backgroundColor: [
                '#3182ce', '#38a169', '#e53e3e', '#dd6b20', '#805ad5',
                '#d53f8c', '#319795', '#9f7aea', '#f6ad55', '#68d391'
            ]
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'right' }
        }
    }
});
</script>

</body>
</html>