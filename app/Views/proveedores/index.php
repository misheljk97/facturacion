<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Gestión de Proveedores
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Administración de Proveedores
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header d-flex justify-content-between align-items-center py-3">
            <h6 class="m-0 font-weight-bold text-primary">Listado General</h6>
            <button type="button" class="btn btn-primary btn-sm" onclick="abrirModal()">
                <i class="bi bi-plus-lg"></i> Nuevo Proveedor
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped w-100" id="tablaProveedores">
                    <thead>
                        <tr>
                            <th style="width: 8%;">ID</th>
                            <th>Identificación / RUC</th>
                            <th>Razón Social / Nombre</th>
                            <th>Teléfono</th>
                            <th style="width: 15%;" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Crear/Editar Proveedor -->
<div class="modal fade" id="modalProveedor" tabindex="-1" aria-labelledby="modalProveedorLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalProveedorLabel">Proveedor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form id="formProveedor">
                <div class="modal-body">
                    <input type="hidden" id="id_proveedor" name="id_proveedor">
                    <div class="mb-3">
                        <label for="identificacion" class="form-label">Identificación / Cédula / RUC <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="identificacion" name="identificacion" placeholder="Ej. 1712345678 o 1712345678001" maxlength="20">
                        <div class="invalid-feedback" id="error-identificacion"></div>
                    </div>
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre / Razón Social <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Ej. Distribuidora S.A.">
                        <div class="invalid-feedback" id="error-nombre"></div>
                    </div>
                    <div class="mb-3">
                        <label for="telefono" class="form-label">Teléfono</label>
                        <input type="text" class="form-control" id="telefono" name="telefono" placeholder="Ej. 0991234567">
                        <div class="invalid-feedback" id="error-telefono"></div>
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
let tablaProveedores;
const baseUrl = "<?= base_url() ?>";

$(document).ready(function() {
    // Inicialización de DataTable
    tablaProveedores = $('#tablaProveedores').DataTable({
        "ajax": {
            "url": baseUrl + "proveedores/listar",
            "type": "GET",
            "dataSrc": "data"
        },
        "columns": [
            { "data": "id_proveedor" },
            { "data": "identificacion" },
            { "data": "nombre" },
            { "data": "telefono", "render": function(data) { return data ? data : '-'; } },
            {
                "data": null,
                "className": "text-center",
                "orderable": false,
                "render": function(data, type, row) {
                    return `
                        <button class="btn btn-sm btn-warning me-1" onclick="editarProveedor(${row.id_proveedor})" title="Editar">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="eliminarProveedor(${row.id_proveedor})" title="Eliminar">
                            <i class="bi bi-trash"></i>
                        </button>
                    `;
                }
            }
        ]
    });

    // Guardar / Actualizar
    $('#formProveedor').on('submit', function(e) {
        e.preventDefault();
        const $form = $(this);
        limpiarErroresFormulario($form);

        $.ajax({
            url: baseUrl + "proveedores/guardar",
            type: "POST",
            data: $form.serialize(),
            dataType: "json",
            success: function(response) {
                if (response.status === 'success') {
                    $('#modalProveedor').modal('hide');
                    tablaProveedores.ajax.reload(null, false);
                    mostrarToast('success', response.message || 'Proveedor guardado correctamente');
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
    const $form = $('#formProveedor');
    $form[0].reset();
    $('#id_proveedor').val('');
    limpiarErroresFormulario($form);
    $('#modalProveedorLabel').text('Nuevo Proveedor');
    $('#modalProveedor').modal('show');
}

function editarProveedor(id) {
    const $form = $('#formProveedor');
    limpiarErroresFormulario($form);

    $.get(baseUrl + "proveedores/obtener/" + id)
        .done(function(response) {
            if (response.status === 'success') {
                $('#id_proveedor').val(response.data.id_proveedor);
                $('#identificacion').val(response.data.identificacion);
                $('#nombre').val(response.data.nombre);
                $('#telefono').val(response.data.telefono);
                $('#modalProveedorLabel').text('Editar Proveedor');
                $('#modalProveedor').modal('show');
            } else {
                mostrarToast('error', response.message || 'Error al obtener datos');
            }
        })
        .fail(function() {
            Swal.fire('Error', 'No se pudo comunicar con el servidor.', 'error');
        });
}

function eliminarProveedor(id) {
    confirmarEliminacion({
        url: baseUrl + "proveedores/eliminar/" + id,
        datatable: tablaProveedores
    });
}
</script>
<?= $this->endSection() ?>
