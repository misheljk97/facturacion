<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Gestión de Productos
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Administración de Productos
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header d-flex justify-content-between align-items-center py-3">
            <h6 class="m-0 font-weight-bold text-primary">Listado de Productos</h6>
            <button type="button" class="btn btn-primary btn-sm" onclick="abrirModal()">
                <i class="bi bi-plus-lg"></i> Nuevo Producto
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped w-100" id="tablaProductos">
                    <thead>
                        <tr>
                            <th style="width: 5%;">ID</th>
                            <th>Código de Barras</th>
                            <th>Producto</th>
                            <th>Categoría</th>
                            <th>Marca</th>
                            <th>Precio Venta ($)</th>
                            <th>Stock</th>
                            <th style="width: 12%;" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Crear/Editar Producto -->
<div class="modal fade" id="modalProducto" tabindex="-1" aria-labelledby="modalProductoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalProductoLabel">Producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form id="formProducto">
                <div class="modal-body">
                    <input type="hidden" id="id_producto" name="id_producto">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="codigo_barras" class="form-label">Código de Barras <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="codigo_barras" name="codigo_barras" placeholder="Ej. 786100012345">
                            <div class="invalid-feedback" id="error-codigo_barras"></div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="nombre" class="form-label">Nombre del Producto <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Ej. Teclado Mecánico RGB">
                            <div class="invalid-feedback" id="error-nombre"></div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="id_categoria" class="form-label">Categoría <span class="text-danger">*</span></label>
                            <select class="form-select form-control" id="id_categoria" name="id_categoria">
                                <option value="">Seleccione una categoría...</option>
                                <?php if (!empty($categorias)): ?>
                                    <?php foreach ($categorias as $cat): ?>
                                        <option value="<?= $cat['id_categoria'] ?>"><?= esc($cat['nombre']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <div class="invalid-feedback" id="error-id_categoria"></div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="id_marca" class="form-label">Marca <span class="text-danger">*</span></label>
                            <select class="form-select form-control" id="id_marca" name="id_marca">
                                <option value="">Seleccione una marca...</option>
                                <?php if (!empty($marcas)): ?>
                                    <?php foreach ($marcas as $mar): ?>
                                        <option value="<?= $mar['id_marca'] ?>"><?= esc($mar['nombre']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <div class="invalid-feedback" id="error-id_marca"></div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="precio_venta" class="form-label">Precio de Venta ($) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" class="form-control" id="precio_venta" name="precio_venta" placeholder="0.00">
                            <div class="invalid-feedback" id="error-precio_venta"></div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="stock" class="form-label">Stock Actual <span class="text-danger">*</span></label>
                            <input type="number" min="0" class="form-control" id="stock" name="stock" placeholder="0">
                            <div class="invalid-feedback" id="error-stock"></div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnGuardar">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
let tablaProductos;
const baseUrl = "<?= base_url() ?>";

$(document).ready(function() {
    // Inicialización de DataTable
    tablaProductos = $('#tablaProductos').DataTable({
        "ajax": {
            "url": baseUrl + "productos/listar",
            "type": "GET",
            "dataSrc": "data"
        },
        "columns": [
            { "data": "id_producto" },
            { "data": "codigo_barras" },
            { "data": "nombre" },
            { "data": "categoria_nombre", "render": function(data) { return data ? data : '<span class="text-muted">Sin Categoría</span>'; } },
            { "data": "marca_nombre", "render": function(data) { return data ? data : '<span class="text-muted">Sin Marca</span>'; } },
            { 
                "data": "precio_venta",
                "render": function(data) {
                    return '$' + parseFloat(data).toFixed(2);
                }
            },
            { 
                "data": "stock",
                "render": function(data) {
                    let cant = parseInt(data);
                    if (cant <= 5) {
                        return `<span class="badge bg-danger text-white">${cant}</span>`;
                    } else if (cant <= 15) {
                        return `<span class="badge bg-warning text-dark">${cant}</span>`;
                    }
                    return `<span class="badge bg-success text-white">${cant}</span>`;
                }
            },
            {
                "data": null,
                "className": "text-center",
                "orderable": false,
                "render": function(data, type, row) {
                    return `
                        <button class="btn btn-sm btn-warning me-1" onclick="editarProducto(${row.id_producto})" title="Editar">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="eliminarProducto(${row.id_producto})" title="Eliminar">
                            <i class="bi bi-trash"></i>
                        </button>
                    `;
                }
            }
        ]
    });

    // Guardar / Actualizar
    $('#formProducto').on('submit', function(e) {
        e.preventDefault();
        const $form = $(this);
        limpiarErroresFormulario($form);

        $.ajax({
            url: baseUrl + "productos/guardar",
            type: "POST",
            data: $form.serialize(),
            dataType: "json",
            success: function(response) {
                if (response.status === 'success') {
                    $('#modalProducto').modal('hide');
                    tablaProductos.ajax.reload(null, false);
                    mostrarToast('success', response.message || 'Producto guardado correctamente');
                } else {
                    if (response.errors) {
                        mostrarErroresFormulario($form, response.errors);
                    } else if (response.message) {
                        Swal.fire('Atención', response.message, 'warning');
                    }
                }
            },
            error: function() {
                Swal.fire('Error', 'Ocurrió un problema en la solicitud.', 'error');
            }
        });
    });
});

function abrirModal() {
    const $form = $('#formProducto');
    $form[0].reset();
    $('#id_producto').val('');
    limpiarErroresFormulario($form);
    $('#modalProductoLabel').text('Nuevo Producto');
    $('#modalProducto').modal('show');
}

function editarProducto(id) {
    const $form = $('#formProducto');
    limpiarErroresFormulario($form);

    $.get(baseUrl + "productos/obtener/" + id)
        .done(function(response) {
            if (response.status === 'success') {
                $('#id_producto').val(response.data.id_producto);
                $('#codigo_barras').val(response.data.codigo_barras);
                $('#nombre').val(response.data.nombre);
                $('#id_categoria').val(response.data.id_categoria);
                $('#id_marca').val(response.data.id_marca);
                $('#precio_venta').val(response.data.precio_venta);
                $('#stock').val(response.data.stock);
                $('#modalProductoLabel').text('Editar Producto');
                $('#modalProducto').modal('show');
            } else {
                mostrarToast('error', response.message || 'Error al obtener datos');
            }
        })
        .fail(function() {
            Swal.fire('Error', 'No se pudo comunicar con el servidor.', 'error');
        });
}

function eliminarProducto(id) {
    confirmarEliminacion({
        url: baseUrl + "productos/eliminar/" + id,
        datatable: tablaProductos
    });
}
</script>
<?= $this->endSection() ?>