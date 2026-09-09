<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid px-4 py-3">

    <!-- Cabecera con Botón de Historial -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">
            <i class="fas fa-truck-loading me-2 text-primary"></i>Ingreso de Mercadería (Compras)
        </h2>
        <a href="<?= base_url('compras/historial') ?>" class="btn btn-outline-primary fw-bold">
            <i class="fas fa-history me-1"></i> Ver Historial de Compras
        </a>
    </div>

    <!-- Alertas de Sesión -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-1"></i> <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-1"></i> <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <form action="<?= base_url('compras/guardar') ?>" method="POST" id="form-compra">
        <?= csrf_field() ?>
        <div class="row">
            <!-- Columna Izquierda: Selección de Proveedor y Formulario de Producto -->
            <div class="col-lg-4">
                <!-- 1. Selección de Proveedor -->
                <div class="card shadow-sm border-0 mb-3">
                    <div class="card-header bg-primary text-white fw-bold">
                        1. Proveedor
                    </div>
                    <div class="card-body">
                        <select name="id_proveedor" id="id_proveedor" class="form-select" required>
                            <option value="">-- Seleccione Proveedor --</option>
                            <?php foreach ($proveedores as $prov): ?>
                                <option value="<?= $prov['id_proveedor'] ?>"><?= esc($prov['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- 2. Formulario Agregar Producto -->
                <div class="card shadow-sm border-0 mb-3">
                    <div class="card-header bg-dark text-white fw-bold">
                        2. Agregar Producto
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Producto</label>
                            <select id="select_producto" class="form-select">
                                <option value="">-- Seleccionar --</option>
                                <?php foreach ($productos as $prod): ?>
                                    <option value="<?= $prod['id_producto'] ?>" data-nombre="<?= esc($prod['nombre']) ?>" data-precio="<?= $prod['precio'] ?? 0 ?>">
                                        <?= esc($prod['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Cantidad a ingresar</label>
                            <input type="number" id="input_cantidad" class="form-control" value="1" min="1">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Costo Unitario ($)</label>
                            <input type="number" step="0.01" id="input_costo" class="form-control" placeholder="0.00" min="0">
                        </div>

                        <button type="button" id="btn_agregar" class="btn btn-success w-100 fw-bold">
                            <i class="fas fa-plus me-1"></i> Añadir a la lista
                        </button>
                    </div>
                </div>
            </div>

            <!-- Columna Derecha: Tabla de Detalle de Compra -->
            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                        <h5 class="mb-0 fw-bold">Detalle de Ingreso</h5>
                        <h4 class="mb-0 fw-bold text-primary">Total: $<span id="lbl_total">0.00</span></h4>
                        <input type="hidden" name="total_compra" id="input_total_compra" value="0.00">
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" id="tabla_detalles">
                                <thead class="table-light">
                                    <tr>
                                        <th>Producto</th>
                                        <th style="width: 100px;">Cant.</th>
                                        <th style="width: 120px;">Costo U.</th>
                                        <th style="width: 120px;">Subtotal</th>
                                        <th style="width: 80px;" class="text-center">Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr id="row_empty">
                                        <td colspan="5" class="text-center text-muted py-4">
                                            No hay productos añadidos a la compra.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-white text-end py-3">
                        <button type="submit" class="btn btn-primary btn-lg fw-bold px-4">
                            <i class="fas fa-save me-1"></i> Registrar Ingreso de Mercadería
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Script interactivo para manejar la tabla dinamica de productos -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    let itemIndex = 0;

    document.getElementById('btn_agregar').addEventListener('click', function () {
        const selectProd = document.getElementById('select_producto');
        const prodId = selectProd.value;
        const prodNombre = selectProd.options[selectProd.selectedIndex]?.getAttribute('data-nombre');
        const cantidad = parseInt(document.getElementById('input_cantidad').value) || 0;
        const costo = parseFloat(document.getElementById('input_costo').value) || 0;

        if (!prodId || cantidad <= 0 || costo <= 0) {
            alert('Por favor complete todos los campos correctamente.');
            return;
        }

        const subtotal = cantidad * costo;
        const emptyRow = document.getElementById('row_empty');
        if (emptyRow) emptyRow.remove();

        const tbody = document.querySelector('#tabla_detalles tbody');
        const tr = document.createElement('tr');
        tr.className = 'item-row';
        tr.innerHTML = `
            <td>
                ${prodNombre}
                <input type="hidden" name="productos[${itemIndex}][id_producto]" value="${prodId}">
            </td>
            <td>
                ${cantidad}
                <input type="hidden" name="productos[${itemIndex}][cantidad]" value="${cantidad}">
            </td>
            <td>
                $${costo.toFixed(2)}
                <input type="hidden" name="productos[${itemIndex}][costo_unitario]" value="${costo}">
            </td>
            <td class="fw-bold">
                $${subtotal.toFixed(2)}
                <input type="hidden" class="subtotal-val" name="productos[${itemIndex}][subtotal]" value="${subtotal}">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger btn-eliminar">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;

        tbody.appendChild(tr);
        itemIndex++;
        recalcularTotal();

        // Reset campos
        selectProd.value = '';
        document.getElementById('input_cantidad').value = '1';
        document.getElementById('input_costo').value = '';
    });

    document.querySelector('#tabla_detalles').addEventListener('click', function (e) {
        if (e.target.closest('.btn-eliminar')) {
            e.target.closest('tr').remove();
            recalcularTotal();
        }
    });

    function recalcularTotal() {
        let total = 0;
        document.querySelectorAll('.subtotal-val').forEach(input => {
            total += parseFloat(input.value) || 0;
        });

        document.getElementById('lbl_total').innerText = total.toFixed(2);
        document.getElementById('input_total_compra').value = total.toFixed(2);

        const tbody = document.querySelector('#tabla_detalles tbody');
        if (tbody.children.length === 0) {
            tbody.innerHTML = `
                <tr id="row_empty">
                    <td colspan="5" class="text-center text-muted py-4">
                        No hay productos añadidos a la compra.
                    </td>
                </tr>
            `;
        }
    }
});
</script>
<?= $this->endSection() ?>