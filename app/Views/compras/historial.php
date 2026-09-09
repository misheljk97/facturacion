<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid px-4 py-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="fw-bold mb-0"><i class="fas fa-history me-2 text-primary"></i>Historial de Ingresos de Mercadería</h2>
        <a href="<?= base_url('compras') ?>" class="btn btn-primary fw-bold">
            <i class="fas fa-plus me-1"></i> Nuevo Ingreso
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nº Compra</th>
                            <th>Fecha</th>
                            <th>Proveedor</th>
                            <th>Registrado por</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($compras)): ?>
                            <?php foreach ($compras as $c): ?>
                                <tr>
                                    <td class="fw-bold">#<?= str_pad($c['id_compra'], 5, '0', STR_PAD_LEFT) ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($c['fecha'])) ?></td>
                                    <td><?= esc($c['proveedor']) ?></td>
                                    <td><?= esc($c['usuario']) ?></td>
                                    <td class="text-end fw-bold text-success">$<?= number_format($c['total'], 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">No se han registrado compras aún.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>