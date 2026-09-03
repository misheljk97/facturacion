<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Gestión de Categorías
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Administración de Categorías
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header d-flex justify-content-between align-items-center py-3">
            <h6 class="m-0 font-weight-bold text-primary">Listado General</h6>
            <button type="button" class="btn btn-primary btn-sm" onclick="abrirModal()">
                <i class="bi bi-plus-lg"></i> Nueva Categoría
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped w-100" id="tablaCategorias">
                    <thead>
                        <tr>
                            <th style="width: 10%;">ID</th>
                            <th>Nombre de la Categoría</th>
                            <th style="width: 15%;" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Crear/Editar Categoría -->
<div class="modal fade" id="modalCategoria" tabindex="-1" aria-labelledby="modalCategoriaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCategoriaLabel">Categoría</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form id="formCategoria">
                <div class="modal-body">
                    <input type="hidden" id="id_categoria" name="id_categoria">
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Ej. Lácteos, Bebidas, etc.">
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
let tablaCategorias;
const baseUrl = "<?= base_url() ?>";

$(document).ready(function() {
    // Inicialización de DataTable (aprovecha la configuración de idioma predeterminada global)
    tablaCategorias = $('#tablaCategorias').DataTable({
        "ajax": {
            "url": baseUrl + "categorias/listar",
            "type": "GET",
            "dataSrc": "data"
        },
        "columns": [
            { "data": "id_categoria" },
            { "data": "nombre" },
            {
                "data": null,
                "className": "text-center",
                "orderable": false,
                "render": function(data, type, row) {
                    return `
                        <button class="btn btn-sm btn-warning me-1" onclick="editarCategoria(${row.id_categoria})" title="Editar">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="eliminarCategoria(${row.id_categoria})" title="Eliminar">
                            <i class="bi bi-trash"></i>
                        </button>
                    `;
                }
            }
        ]
    });

    // Guardar / Actualizar
    $('#formCategoria').on('submit', function(e) {
        e.preventDefault();
        const $form = $(this);
        limpiarErroresFormulario($form);

        $.ajax({
            url: baseUrl + "categorias/guardar",
            type: "POST",
            data: $form.serialize(),
            dataType: "json",
            success: function(response) {
                if (response.status === 'success') {
                    $('#modalCategoria').modal('hide');
                    tablaCategorias.ajax.reload(null, false);
                    mostrarToast('success', response.message || 'Categoría guardada correctamente');
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
    const $form = $('#formCategoria');
    $form[0].reset();
    $('#id_categoria').val('');
    limpiarErroresFormulario($form);
    $('#modalCategoriaLabel').text('Nueva Categoría');
    $('#modalCategoria').modal('show');
}

function editarCategoria(id) {
    const $form = $('#formCategoria');
    limpiarErroresFormulario($form);

    $.get(baseUrl + "categorias/obtener/" + id)
        .done(function(response) {
            if (response.status === 'success') {
                $('#id_categoria').val(response.data.id_categoria);
                $('#nombre').val(response.data.nombre);
                $('#modalCategoriaLabel').text('Editar Categoría');
                $('#modalCategoria').modal('show');
            } else {
                mostrarToast('error', response.message || 'Error al obtener datos');
            }
        })
        .fail(function() {
            Swal.fire('Error', 'No se pudo comunicar con el servidor.', 'error');
        });
}

function eliminarCategoria(id) {
    // Llamada al helper global reutilizable
    confirmarEliminacion({
        url: baseUrl + "categorias/eliminar/" + id,
        datatable: tablaCategorias
    });
}
</script>
<?= $this->endSection() ?>