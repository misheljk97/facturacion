<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Gestión de Clientes
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Administración de Clientes
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header d-flex justify-content-between align-items-center py-3">
            <h6 class="m-0 font-weight-bold text-primary">Listado General</h6>
            <button type="button" class="btn btn-primary btn-sm" onclick="abrirModal()">
                <i class="bi bi-plus-lg"></i> Nuevo Cliente
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped w-100" id="tablaClientes">
                    <thead>
                        <tr>
                            <th style="width: 8%;">ID</th>
                            <th>Identificación / Cédula</th>
                            <th>Nombre Completo</th>
                            <th>Teléfono</th>
                            <th>Correo Electrónico</th>
                            <th style="width: 15%;" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Crear/Editar Cliente -->
<div class="modal fade" id="modalCliente" tabindex="-1" aria-labelledby="modalClienteLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalClienteLabel">Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form id="formCliente">
                <div class="modal-body">
                    <input type="hidden" id="id_cliente" name="id_cliente">
                    <div class="mb-3">
                        <label for="identificacion" class="form-label">Identificación / Cédula <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="identificacion" name="identificacion" placeholder="Ej. 1712345678" maxlength="20">
                        <div class="invalid-feedback" id="error-identificacion"></div>
                    </div>
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre Completo <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Ej. Juan Pérez">
                        <div class="invalid-feedback" id="error-nombre"></div>
                    </div>
                    <div class="mb-3">
                        <label for="telefono" class="form-label">Teléfono</label>
                        <input type="text" class="form-control" id="telefono" name="telefono" placeholder="Ej. 0991234567">
                        <div class="invalid-feedback" id="error-telefono"></div>
                    </div>
                    <div class="mb-3">
                        <label for="correo" class="form-label">Correo Electrónico</label>
                        <input type="email" class="form-control" id="correo" name="correo" placeholder="Ej. cliente@ejemplo.com">
                        <div class="invalid-feedback" id="error-correo"></div>
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
let tablaClientes;
const baseUrl = "<?= base_url() ?>";

$(document).ready(function() {
    // Inicialización de DataTable
    tablaClientes = $('#tablaClientes').DataTable({
        "ajax": {
            "url": baseUrl + "clientes/listar",
            "type": "GET",
            "dataSrc": "data"
        },
        "columns": [
            { "data": "id_cliente" },
            { "data": "identificacion" },
            { "data": "nombre" },
            { "data": "telefono", "render": function(data) { return data ? data : '-'; } },
            { "data": "correo", "render": function(data) { return data ? data : '-'; } },
            {
                "data": null,
                "className": "text-center",
                "orderable": false,
                "render": function(data, type, row) {
                    return `
                        <button class="btn btn-sm btn-warning me-1" onclick="editarCliente(${row.id_cliente})" title="Editar">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="eliminarCliente(${row.id_cliente})" title="Eliminar">
                            <i class="bi bi-trash"></i>
                        </button>
                    `;
                }
            }
        ]
    });

    // Guardar / Actualizar
    $('#formCliente').on('submit', function(e) {
        e.preventDefault();
        const $form = $(this);
        limpiarErroresFormulario($form);

        $.ajax({
            url: baseUrl + "clientes/guardar",
            type: "POST",
            data: $form.serialize(),
            dataType: "json",
            success: function(response) {
                if (response.status === 'success') {
                    $('#modalCliente').modal('hide');
                    tablaClientes.ajax.reload(null, false);
                    mostrarToast('success', response.message || 'Cliente guardado correctamente');
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
    const $form = $('#formCliente');
    $form[0].reset();
    $('#id_cliente').val('');
    limpiarErroresFormulario($form);
    $('#modalClienteLabel').text('Nuevo Cliente');
    $('#modalCliente').modal('show');
}

function editarCliente(id) {
    const $form = $('#formCliente');
    limpiarErroresFormulario($form);

    $.get(baseUrl + "clientes/obtener/" + id)
        .done(function(response) {
            if (response.status === 'success') {
                $('#id_cliente').val(response.data.id_cliente);
                $('#identificacion').val(response.data.identificacion);
                $('#nombre').val(response.data.nombre);
                $('#telefono').val(response.data.telefono);
                $('#correo').val(response.data.correo);
                $('#modalClienteLabel').text('Editar Cliente');
                $('#modalCliente').modal('show');
            } else {
                mostrarToast('error', response.message || 'Error al obtener datos');
            }
        })
        .fail(function() {
            Swal.fire('Error', 'No se pudo comunicar con el servidor.', 'error');
        });
}

function eliminarCliente(id) {
    confirmarEliminacion({
        url: baseUrl + "clientes/eliminar/" + id,
        datatable: tablaClientes
    });
}
</script>
<?= $this->endSection() ?>