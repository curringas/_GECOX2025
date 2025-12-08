
import Sortable from 'sortablejs';

function ajaxGuardarOrden(tabla, orden) {
    console.log('Guardando orden para tabla:', tabla, 'con orden:', orden);
    $.ajax({
        // Usaremos la URL que ya definiste globalmente
        url: window.reordenarUrl,
        method: 'POST',
        data: {
            _token: window.csrfToken,
            tabla: tabla,
            orden_ids: orden // Array de IDs ordenados
        },
        success: function (response) {
            if (response.success) {
                console.log('Orden guardado: ', response.Message);

                // **** PASO CRÍTICO: SINCRONIZAR DATA-ORDEN EN EL FRONT-END ****
                // Iteramos sobre los IDs recibidos en el mismo orden que fueron enviados.
                // Como el backend los actualizará a 1, 2, 3...

                orden.forEach(function (id, index) {
                    var nuevoValorOrden = index + 1; // 0-indexed a 1-indexed (1, 2, 3...)

                    // Buscamos el contenedor del banner por su data-id
                    var $bannerContainer = $('[data-id="' + id + '"]');

                    // Encontramos los botones dentro de ese contenedor y actualizamos su data-orden
                    $bannerContainer.find('[data-orden]').attr('data-orden', nuevoValorOrden);
                });

            } else {
                alert('Error al guardar el orden: ' + (response.message || 'Ha ocurrido un error inesperado.'));
                // Si hay un error, lo ideal sería recargar o revertir, pero de momento con el alert basta.
            }
        },
        error: function (xhr) {
            alert('Error de conexión al reordenar.');
        }
    });
}

$(function() {

    // Selelct2 solo para Noticias
    var $modalNoticia = $('#noticia'); 

    $('.select2').select2({ 
        width: '100%',
        minimumInputLength: 3, 
        placeholder: "Escriba para buscar una publicación...",
        
        // AÑADE ESTA LÍNEA CRÍTICA
        dropdownParent: $modalNoticia, 

        ajax: {
            url: window.publicacionBuscarUrl,
            dataType: 'json',
            delay: 250, 
            
            data: function (params) {
                return {
                    term: params.term
                };
            },
            
            processResults: function (data) {
                return {
                    results: data.results
                };
            },
            cache: true
        }
    });

    // ******************************************************************************
    // ********  Hacemos ordenable cualquier lista con .lista-ordenable */
    //******************************************************************************
    $('.lista-ordenable').each(function(index, element) {
        var listaContainer = element;
        var tablaKey = $(listaContainer).data('tabla');
        new Sortable(element, {
            group: 'shared',
            animation: 150,
            onEnd: function (evt) {
                // Si solo estás moviendo elementos DENTRO de la misma lista
                if (evt.from === evt.to) { 
                    
                    // Obtener el nuevo orden de los IDs
                    var nuevoOrden = [];
                    $(listaContainer).children('div').each(function(i, el) {
                        nuevoOrden.push($(el).data('id'));
                    });

                    // Llamada AJAX para guardar el nuevo orden
                    ajaxGuardarOrden(tablaKey, nuevoOrden);
                }
            },
        });
    });

    //******************************************************************************
    // ******** ELIMINAR CUALQUIER ITEM */
    //******************************************************************************
    $('[data-eliminar]').on('click', function(e) {
        e.preventDefault();

        var eliminarKey = $(this).data('eliminar');
        var tablaKey = $(this).data('tabla');
        var ordenKey = $(this).data('orden');
        var idKey = $(this).data('id');

        if (confirm('¿Estás seguro de que deseas eliminar este banner?')) {
            $.ajax({
                url: window.eliminarUrl,
                method: 'POST',
                data: {
                    _token: window.csrfToken,
                    eliminar: eliminarKey,
                    tabla: tablaKey,
                    orden: ordenKey,
                    id: idKey
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

    //******************************************************************************
    // ******** NOTICIA ************************************************************
    //******************************************************************************

    // ******** NOTICIA --- Abrimos el modal tipo noticia y cargamos la infonrmacion que corresponda */

    // Al hacer click en cualquier botón de editar banner [data-banner]]
    $('[data-noticia]').on('click', function(e) {
        var tablaKey = $(this).data('tabla');
        var ordenKey = $(this).data('orden');
        var idKey = $(this).data('id');

        if (ordenKey === 'nuevo') {
            // Si es un nuevo banner, limpiamos los campos y abrimos el modal directamente
            var modal = $('#noticia');
            var $selectPublicacion = modal.find('#noticiaPublicacion');
                
            // Limpiar selecciones anteriores y opciones dinámicas
            $selectPublicacion.empty();
            $selectPublicacion.append('<option value="">-- Selecciona una publicación --</option>').trigger('change');
            modal.find('#noticiaTabla').val(tablaKey);
            modal.find('#noticiaIdentificador').val('nuevo');
            modal.find('#noticiaOrden').val('nuevo');
            modal.modal('show'); // Abre el modal
            return; // Salimos de la función
        }

        // Llamada AJAX al controlador
        $.ajax({
            url: window.noticiaDatosUrl,
            method: 'POST',
            data: {
                _token: window.csrfToken,
                tabla: tablaKey,
                orden: ordenKey,
                id: idKey
            },
            success: function(data) {
                var modal = $('#noticia');
                var $selectPublicacion = modal.find('#noticiaPublicacion');
                
                // 1. Limpiar selecciones anteriores y opciones dinámicas
                $selectPublicacion.empty();
                
                // Si existe un ID de publicación (estamos editando)
                if (data.publicacion) {
                    
                    // 2. CREAR Y AÑADIR LA OPCIÓN ACTUAL
                    var nuevaOpcion = new Option(data.titulo, data.publicacion, true, true);
                    $selectPublicacion.append(nuevaOpcion).trigger('change');
                } else {
                    // Si no hay publicación seleccionada, dejar el placeholder
                    $selectPublicacion.append('<option value="">-- Selecciona una publicación --</option>').trigger('change');
                }
                modal.find('#noticiaTabla').val(tablaKey);
                modal.find('#noticiaIdentificador').val(idKey);
                modal.find('#noticiaOrden').val(ordenKey);
                modal.find('#noticiaPublicacion').val(data.publicacion || '');
                modal.modal('show'); // Abre el modal después de cargar los datos
            },
            error: function(xhr) {
                alert('Error al cargar datos del banner');
            }
        });

        // Evita que el modal se abra antes de cargar los datos
        e.preventDefault();
    });



    // ******** NOTICIA --- Recoger y guardar el formulario del modal *****************************

    $('#noticia form').on('submit', function(e) {
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
                var errorMessage = 'Error al guardar la noticia.';
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
    // ******** BANNER --- *********************************************************
    //******************************************************************************

    // ******** OBTENER --- Abrimos el modal tipo banner y cargamos la infonrmacion que corresponda */

    // Al hacer click en cualquier botón de editar banner [data-banner]]
    $('[data-banner]').on('click', function(e) {
        var bannerKey = $(this).data('banner');
        var tablaKey = $(this).data('tabla');
        var ordenKey = $(this).data('orden');
        var idKey = $(this).data('id');

        if (ordenKey === 'nuevo') {
            // Si es un nuevo banner, limpiamos los campos y abrimos el modal directamente
            var modal = $('#banner');
            modal.find('#bannerTabla').val(tablaKey);
            modal.find('#bannerBanner').val(bannerKey);
            modal.find('#bannerIdentificador').val('nuevo');
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
                orden: ordenKey,
                id: idKey
            },
            success: function(data) {
                var modal = $('#banner');
                modal.find('#bannerTabla').val(tablaKey);
                modal.find('#bannerBanner').val(bannerKey);
                modal.find('#bannerIdentificador').val(idKey);
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


    // ******** GUARDAR --- Recoger y guardar el formulario del modal */

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

    
});