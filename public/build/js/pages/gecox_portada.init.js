
$(function() {


    //******************************************************************************
    // ******** BANNER --- Abrimos el modal tipo banner y cargamos la infonrmacion que corresponda */
    //******************************************************************************

    // Al hacer click en cualquier botón de editar banner [data-banner]]
    $('[data-banner]').on('click', function(e) {
        var bannerKey = $(this).data('banner');
        var tablaKey = $(this).data('tabla');
        var ordenKey = $(this).data('orden');

        if (ordenKey === 'nuevo') {
            // Si es un nuevo banner, limpiamos los campos y abrimos el modal directamente
            var modal = $('#banner');
            modal.find('#bannerTabla').val(tablaKey);
            modal.find('#bannerBanner').val(bannerKey);
            modal.find('#bannerOrden').val('nuevo');
            modal.find('#bannerTitulo').val('');
            modal.find('#bannerUrl').val('');
            modal.find('#bannerDestino').val('');
            modal.find('#bannerCodigo').val('');
            modal.modal('show'); // Abre el modal
            return; // Salimos de la función
        }

        // Llamada AJAX al controlador
        $.ajax({
            url: window.bannerDatosUrl,
            method: 'POST',
            data: {
                _token: window.csrfToken,
                banner: bannerKey,
                tabla: tablaKey,
                orden: ordenKey
            },
            success: function(data) {
                var modal = $('#banner');
                modal.find('#bannerTabla').val(tablaKey);
                modal.find('#bannerBanner').val(bannerKey);
                modal.find('#bannerOrden').val(ordenKey);
                modal.find('#bannerTitulo').val(data.titulo || '');
                modal.find('#bannerUrl').val(data.url || '');
                modal.find('#bannerDestino').val(data.destino || '');
                modal.find('#bannerCodigo').val(data.codigo || '');
                modal.modal('show'); // Abre el modal después de cargar los datos
            },
            error: function(xhr) {
                alert('Error al cargar datos del banner');
            }
        });

        // Evita que el modal se abra antes de cargar los datos
        e.preventDefault();
    });

    //******************************************************************************
    // ******** BANNER --- Recoger y guardar el formulario del modal */
    //******************************************************************************

    $('#banner form').on('submit', function(e) {
        e.preventDefault(); // Evita el envío tradicional del formulario

        var form = $(this);
        var formData = new FormData(form[0]); // Para enviar archivos (como la imagen)

        // Agregamos el token CSRF manualmente al FormData
        formData.append('_token', window.csrfToken);

        // Deshabilitar el botón de guardar para evitar múltiples envíos
        var submitButton = form.find('button[type="submit"]');
        submitButton.attr('disabled', true).text('Guardando...');

        $.ajax({
            url: form.attr('action'), // Usamos la URL de la acción del formulario
            method: 'POST',
            data: formData,
            processData: false, // Importante para FormData
            contentType: false, // Importante para FormData
            success: function(response) {
                // Verificar si la operación fue exitosa (puedes ajustar esta lógica según la respuesta de tu controlador)
                if (response.success) { 
                    // Cerrar el modal
                    $('#banner').modal('hide');
                    // Recargar la página o actualizar el contenido si es necesario (para ver los cambios reflejados)
                    window.location.reload(); 
                } else {
                    alert('Error al guardar: ' + (response.message || 'Ha ocurrido un error inesperado.'));
                }
            },
            error: function(xhr) {
                // Manejo de errores de validación o del servidor
                var errorMessage = 'Error al guardar el banner.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage += '\nDetalle: ' + xhr.responseJSON.message;
                }
                alert(errorMessage);
            },
            complete: function() {
                // Habilitar el botón de nuevo
                submitButton.attr('disabled', false).text('Guardar cambios');
            }
        });
    });

    //******************************************************************************
    // ******** BANNER --- Eliminar un banner de portada */
    //******************************************************************************
    $('[data-banner-eliminar]').on('click', function(e) {
        e.preventDefault();

        var bannerKey = $(this).data('banner-eliminar');
        var tablaKey = $(this).data('tabla');
        var ordenKey = $(this).data('orden');

        if (confirm('¿Estás seguro de que deseas eliminar este banner?')) {
            $.ajax({
                url: window.bannerEliminarUrl,
                method: 'POST',
                data: {
                    _token: window.csrfToken,
                    banner: bannerKey,
                    tabla: tablaKey,
                    orden: ordenKey
                },
                success: function(response) {
                    if (response.success) {
                        // Recargar la página o actualizar el contenido si es necesario
                        window.location.reload();
                    } else {
                        alert('Error al eliminar: ' + (response.message || 'Ha ocurrido un error inesperado.'));
                    }
                },
                error: function(xhr) {
                    alert('Error al eliminar el banner.');
                }
            });
        }
    });
});