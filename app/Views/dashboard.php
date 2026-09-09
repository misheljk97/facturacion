<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center my-3">
        <div>
            <h2 class="fw-bold mb-0">Decisiones con datos claros</h2>
            <small class="text-muted">Revisa el pulso del negocio y detecta lo que necesita atención hoy.</small>
        </div>
        <span class="badge bg-light text-dark p-2 border"><i class="far fa-calendar-alt me-1"></i> <?= date('d/m/Y') ?></span>
    </div>

    <!-- TARJETAS KPI -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm border-0 border-start border-primary border-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small">Ventas de hoy</div>
                            <div class="h3 fw-bold mb-0"><?= $ventas_hoy ?></div>
                            <small class="text-muted">transacciones registradas</small>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded-circle text-primary">
                            <i class="fas fa-receipt fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm border-0 border-start border-success border-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small">Ingresos del mes</div>
                            <div class="h3 fw-bold mb-0 text-success">$<?= number_format($ingresos_mes, 2) ?></div>
                            <small class="text-muted">acumulado mensual</small>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded-circle text-success">
                            <i class="fas fa-dollar-sign fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm border-0 border-start border-warning border-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small">Clientes registrados</div>
                            <div class="h3 fw-bold mb-0"><?= $total_clientes ?></div>
                            <small class="text-muted">base de clientes</small>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded-circle text-warning">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm border-0 border-start border-danger border-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small">Stock por revisar</div>
                            <div class="h3 fw-bold mb-0 text-danger"><?= $stock_alerta ?></div>
                            <small class="text-muted">productos con 5 o menos unid.</small>
                        </div>
                        <div class="bg-danger bg-opacity-10 p-3 rounded-circle text-danger">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SECCIÓN DE GRÁFICOS -->
    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white font-weight-bold d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-chart-line me-1 text-primary"></i> Actividad de los últimos 7 días</span>
                    <span class="badge bg-info text-dark"><i class="fas fa-sync-alt me-1"></i> Tiempo Real</span>
                </div>
                <div class="card-body">
                    <canvas id="chartActividad7Dias" style="max-height: 280px;"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white font-weight-bold">
                    <i class="fas fa-chart-bar me-1 text-success"></i> Ingresos del Mes Actual
                </div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    <canvas id="chartIngresosMensual" style="max-height: 280px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- TABLAS DE RESUMEN DE DECISIONES -->
    <div class="row g-3 mb-4">
        <div class="col-lg-7">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white font-weight-bold">
                    <i class="fas fa-box-open me-1 text-info"></i> Productos más vendidos
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Producto</th>
                                    <th class="text-center">Unidades</th>
                                    <th class="text-end">Ingresos</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($top_productos)): ?>
                                    <?php foreach($top_productos as $p): ?>
                                    <tr>
                                        <td><?= esc($p['nombre']) ?></td>
                                        <td class="text-center"><span class="badge bg-light text-primary border"><?= $p['unidades'] ?></span></td>
                                        <td class="text-end fw-bold text-success">$<?= number_format($p['ingresos'], 2) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="3" class="text-center text-muted py-3">Sin datos registrados</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white font-weight-bold text-danger">
                    <i class="fas fa-box me-1"></i> Alertas de Inventario
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <?php if(!empty($alertas_stock)): ?>
                            <?php foreach($alertas_stock as $a): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-bold"><?= esc($a['nombre']) ?></div>
                                    <small class="text-muted">Precio: $<?= number_format($a['precio_venta'], 2) ?></small>
                                </div>
                                <span class="badge bg-warning text-dark"><?= $a['stock'] ?> unid.</span>
                            </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li class="list-group-item text-center text-muted py-3">Todo el inventario está en niveles óptimos</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cargar Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener("DOMContentLoaded", () => {
    let chartActividad;

    const ctx7 = document.getElementById('chartActividad7Dias').getContext('2d');
    const ctxIngresos = document.getElementById('chartIngresosMensual').getContext('2d');

    // Inicializar Gráfico Rendimiento 7 días
    chartActividad = new Chart(ctx7, {
        type: 'line',
        data: {
            labels: [],
            datasets: [
                {
                    label: 'Ventas (Unid)',
                    data: [],
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'Ingresos ($)',
                    data: [],
                    borderColor: '#198754',
                    backgroundColor: 'rgba(25, 135, 84, 0.1)',
                    fill: true,
                    tension: 0.4,
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true, title: { display: true, text: 'Ventas' } },
                y1: { beginAtZero: true, position: 'right', title: { display: true, text: 'Ingresos ($)' } }
            }
        }
    });

    // Gráfico de Ingresos del Mes Actual
    new Chart(ctxIngresos, {
        type: 'bar',
        data: {
            labels: ['Mes Actual'],
            datasets: [{
                label: 'Ingresos ($)',
                data: [<?= $ingresos_mes ?>],
                backgroundColor: '#20c997',
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: { y: { beginAtZero: true } }
        }
    });

    // Cargar datos en tiempo real dinámicamente
    function cargarDatosRealTime() {
        fetch('<?= base_url("home/getDataGraficos") ?>')
            .then(res => res.json())
            .then(data => {
                chartActividad.data.labels = data.fechas;
                chartActividad.data.datasets[0].data = data.ventas;
                chartActividad.data.datasets[1].data = data.ingresos;
                chartActividad.update();
            });
    }

    cargarDatosRealTime();
    // Actualizar gráficos en tiempo real cada 30 segundos
    setInterval(cargarDatosRealTime, 30000);
});
</script>
<?= $this->endSection() ?>