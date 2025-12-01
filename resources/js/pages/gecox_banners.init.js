
(function () {
    'use strict'

    // para validacion de campos con required de Bootstrap 5
    document.querySelectorAll('.needs-validation').forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });

    




    // ---------------------------------------------------------------------------------------------
    // ===== IMAGENES - =====================================================
    // ---------------------------------------------------------------------------------------------

    

    // --- LÓGICA DE SUBIDA DE NUEVAS IMÁGENES ---
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Obtener los elementos clave
        const deleteButton = document.getElementById('delete-current-banner');
        const currentBannerPreview = document.getElementById('current-banner-preview');
        const removeBannerInput = document.getElementById('remove_banner');
        const removeBannerNote = document.getElementById('remove-banner-note');

        // 2. Verificar que el botón de eliminar exista (solo existe en modo edición con imagen)
        if (deleteButton) {
            // 3. Agregar el Listener de Eventos al botón
            deleteButton.addEventListener('click', function() {
                // Confirmación opcional para el usuario
                if (confirm('¿Estás seguro de que deseas eliminar la imagen actual del banner?')) {
                    // a. Ocultar el contenedor de la previsualización
                    // Esto simula la eliminación en el cliente
                    currentBannerPreview.style.display = 'none';

                    // b. Actualizar el campo hidden para notificar a Laravel
                    // El valor '1' indica que la imagen debe ser eliminada en el backend
                    removeBannerInput.value = '1';

                    // c. Mostrar la nota de "marcado para eliminación"
                    removeBannerNote.style.display = 'inline';

                    // d. (Opcional) Si el banner tiene el campo 'Banner' (imagen) y no 'Codigo' (script), 
                    // podríamos desbloquear el input de archivo si lo tuvieras, para forzar al usuario a subir una nueva.
                    // Si solo tienes el campo de URL (como en tu código), no es necesario.
                    
                    alert('La imagen ha sido marcada para eliminación. Guarda el formulario para aplicar los cambios.');
                }
            });
        }
    });


})()