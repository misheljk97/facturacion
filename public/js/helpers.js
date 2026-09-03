// ==========================================
// 1. Toast Reutilizable
// ==========================================
const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer);
        toast.addEventListener('mouseleave', Swal.resumeTimer);
    }
});

// Función global helper para disparar Toasts
function mostrarToast(icon, title) {
    Toast.fire({ icon, title });
}

// ==========================================
// 2. DataTables Defaults (Español global)
// ==========================================
if ($.fn.dataTable) {
    $.extend(true, $.fn.dataTable.defaults, {
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
        },
        responsive: true
    });
}

// ==========================================
// 3. Helper Global para Eliminar Registros
// ==========================================
function confirmarEliminacion({ url, datatable, titulo = '¿Estás seguro?', texto = 'Esta acción no se puede deshacer.' }) {
    Swal.fire({
        title: titulo,
        text: texto,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            $.get(url)
                .done(function(response) {
                    if (response.status === 'success') {
                        if (datatable) datatable.ajax.reload(null, false);
                        mostrarToast('success', response.message || 'Registro eliminado correctamente.');
                    } else {
                        Swal.fire('Error', response.message || 'No se pudo eliminar el registro', 'error');
                    }
                })
                .fail(function() {
                    Swal.fire('Error', 'Ocurrió un problema en el servidor', 'error');
                });
        }
    });
}

// ==========================================
// 4. Helper Global para Limpieza de Formulario
// ==========================================
function limpiarErroresFormulario($form) {
    $form.find('.is-invalid').removeClass('is-invalid');
    $form.find('.invalid-feedback').text('');
}

function mostrarErroresFormulario($form, errors) {
    limpiarErroresFormulario($form);
    $.each(errors, function(field, message) {
        const $input = $form.find(`[name="${field}"]`);
        $input.addClass('is-invalid');
        $form.find(`#error-${field}`).text(message);
    });
}