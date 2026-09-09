<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid px-4">
    <h1 class="mt-4">Historial de Facturas</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?= base_url('facturas') ?>">Facturación</a></li>
        <li class="breadcrumb-item active">Historial</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header font-weight-bold d-flex justify-content-between align-items-center">
            <span><i class="fas fa-history me-1"></i> Lista de Ventas Registradas</span>
            <a href="<?= base_url('facturas') ?>" class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-1"></i> Nueva Factura
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col"># ID</th>
                            <th scope="col">Cliente</th>
                            <th scope="col">Total</th>
                            <th scope="col">Fecha</th>
                            <th scope="col" class="text-center" style="width: 120px;">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($ventas)): ?>
                            <?php foreach ($ventas as $v): ?>
                                <tr>
                                    <td class="fw-bold">#<?= $v['id_venta'] ?></td>
                                    <td><?= esc($v['cliente']) ?></td>
                                    <td class="text-success fw-bold">$<?= number_format($v['total'], 2) ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($v['fecha'])) ?></td>
                                    <td class="text-center">
                                        <a href="<?= base_url('facturas/imprimir/' . $v['id_venta']) ?>" target="_blank" class="btn btn-danger btn-sm fw-bold">
                                            <i class="fas fa-file-pdf me-1"></i> PDF
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">No hay facturas registradas.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>