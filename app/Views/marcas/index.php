<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Gestión de Marcas
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Administración de Marcas
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header d-flex justify-content-between align-items-center py-3">
            <h6 class="m-0 font-weight-bold text-primary">Listado General</h6>
            <button type="button" class="btn btn-primary btn-sm" onclick="abrirModal()">
                <i class="bi bi-plus-lg"></i> Nueva Marca
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped w-100" id="tablaMarcas">
                    <thead>
                        <tr>
                            <th style="width: 10%;">ID</th>
                            <th>Nombre de la Marca</th>
                            <th style="width: 15%;" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalMarca" tabindex="-1" aria-labelledby="modalMarcaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalMarcaLabel">Marca</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form id="formMarca">
                <div class="modal-body">
                    <input type="hidden" id="id_marca" name="id_marca">
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Ej. Nike, Samsung, etc.">
                        <div class="invalid-feedback" id="error-nombre"></div>
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
let tablaMarcas;
const baseUrl = "<?= base_url() ?>";

$(document).ready(function() {
    // Inicialización de DataTable
    tablaMarcas = $('#tablaMarcas').DataTable({
        "ajax": {
            "url": baseUrl + "marcas/listar",
            "type": "GET",
            "dataSrc": "data"
        },
        "columns": [
            { "data": "id_marca" },
            { "data": "nombre" },
            {
                "data": null,
                "className": "text-center",
                "orderable": false,
                "render": function(data, type, row) {
                    return `
                        <button class="btn btn-sm btn-warning me-1" onclick="editarMarca(${row.id_marca})" title="Editar">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="eliminarMarca(${row.id_marca})" title="Eliminar">
                            <i class="bi bi-trash"></i>
                        </button>
                    `;
                }
            }
        ]
    });

    // Guardar / Actualizar
    $('#formMarca').on('submit', function(e) {
        e.preventDefault();
        const $form = $(this);
        limpiarErroresFormulario($form);

        $.ajax({
            url: baseUrl + "marcas/guardar",
            type: "POST",
            data: $form.serialize(),
            dataType: "json",
            success: function(response) {
                if (response.status === 'success') {
                    $('#modalMarca').modal('hide');
                    tablaMarcas.ajax.reload(null, false);
                    mostrarToast('success', response.message || 'Marca guardada correctamente');
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
    const $form = $('#formMarca');
    $form[0].reset();
    $('#id_marca').val('');
    limpiarErroresFormulario($form);
    $('#modalMarcaLabel').text('Nueva Marca');
    $('#modalMarca').modal('show');
}

function editarMarca(id) {
    const $form = $('#formMarca');
    limpiarErroresFormulario($form);

    $.get(baseUrl + "marcas/obtener/" + id)
        .done(function(response) {
            if (response.status === 'success') {
                $('#id_marca').val(response.data.id_marca);
                $('#nombre').val(response.data.nombre);
                $('#modalMarcaLabel').text('Editar Marca');
                $('#modalMarca').modal('show');
            } else {
                mostrarToast('error', response.message || 'Error al obtener datos');
            }
        })
        .fail(function() {
            Swal.fire('Error', 'No se pudo comunicar con el servidor.', 'error');
        });
}

function eliminarMarca(id) {
    confirmarEliminacion({
        url: baseUrl + "marcas/eliminar/" + id,
        datatable: tablaMarcas
    });
}
</script>
<?= $this->endSection() ?>