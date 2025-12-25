
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

    // Inicializar TinyMCE para el textarea explicativo WYSYIWYG
    tinymce.init({
        selector: 'textarea#Introduccion, textarea#Contenido',
        height: 350,
        contextmenu: false,
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
            'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
            'insertdatetime', 'media', 'table', 'help', 'wordcount'
        ],
        toolbar: 'undo redo | blocks | ' +
            'bold italic backcolor | alignleft aligncenter ' +
            'alignright alignjustify | bullist numlist outdent indent | ' +
            'removeformat | help',
        content_style: 'body { font-family:"Poppins",sans-serif; font-size:16px }'
    });

    // SEO - Usamos selectores de ID: #Titulo y #Url para el SLUG de la publicación
    setupSlugGenerator('#Titulo', '#Url');

    




    // ---------------------------------------------------------------------------------------------
    // ===== IMAGENES - =====================================================
    // ---------------------------------------------------------------------------------------------

    // --- LÓGICA DE EDICIÓN: ELIMINACIÓN ---
    const imgsAEliminar = [];
    const hiddenImgsAEliminar = $('#imagenes_a_eliminar');
    const previewContainer = $('#previewContainer');

    // Inicializar campo oculto para eliminación
    hiddenImgsAEliminar.val(JSON.stringify(imgsAEliminar));

    // 1. Manejar la eliminación de imágenes existentes (cargadas desde la BD)
    previewContainer.on('click', '.delete-existing-img', function() {
        const item = $(this).closest('.existing-img');
        const compositeKey = item.data('id');

        if (confirm('¿Estás seguro de que deseas eliminar esta imagen?')) {
            // Almacenamos la clave compuesta completa (ej: "64193|0")
            imgsAEliminar.push(compositeKey); 
            
            hiddenImgsAEliminar.val(JSON.stringify(imgsAEliminar));
            
            item.remove();
        }
    });

    // --- LÓGICA DE SUBIDA DE NUEVAS IMÁGENES ---
    const inputImagen = $('#inputGroupFile04');
    const fileMapImagenes = new Map();
    let fileCounterImagenes = 0;
    const MAX_SIZE_BYTES_IMG = 5242880; // 5 MB de ejemplo (Ajusta según necesidad)

    inputImagen.on('change', function() {
        const files = inputImagen[0].files;
        if (!files.length) return;

        let filesToProcess = [];
        let filesRejected = [];

        // Validar tamaño y tipo (aunque el accept="image/*" ayuda, validamos tamaño)
        Array.from(files).forEach((file) => {
            if (!file.type.startsWith('image/')) {
                // Ignorar no imágenes
            } else if (file.size > MAX_SIZE_BYTES_IMG) {
                filesRejected.push(file.name);
            } else {
                filesToProcess.push(file);
            }
        });

        if (filesRejected.length > 0) {
            alert('Las siguientes imágenes superan el límite de 5MB y no han sido añadidas:\n- ' + filesRejected.join('\n- '));
        }

        // Usamos FileReader solo para la previsualización, NO para codificar a Base64 para el POST
        filesToProcess.forEach((file) => {
            const fileId = fileCounterImagenes++;
            fileMapImagenes.set(fileId, file);
            
            // Usamos FileReader solo para generar la miniatura del cliente
            const reader = new FileReader();
            reader.onload = function(e) {
                const base64Url = e.target.result;

                // Miniatura
                const thumb = $(`
                    <div id="img-item-${fileId}" class="position-relative d-inline-block new-img-preview" style="width:120px">
                        <img src="${base64Url}" class="img-thumbnail" style="width:100%; height:100px; object-fit:cover;">
                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1" data-file-id="${fileId}">&times;</button>
                    </div>
                `);

                // Lógica para eliminar del listado y del mapa
                thumb.find('button').on('click', function() {
                    const idToRemove = $(this).data('file-id');
                    fileMapImagenes.delete(idToRemove);
                    thumb.remove();
                    updateFileInputImagenes(); 
                });

                previewContainer.append(thumb);
                updateFileInputImagenes(); // Actualizar el input después de añadir
            };
            reader.readAsDataURL(file); // Lectura rápida para previsualización
        });

        // Función para actualizar el input file con los archivos válidos restantes
        function updateFileInputImagenes() {
            const dataTransfer = new DataTransfer();
            fileMapImagenes.forEach(file => {
                dataTransfer.items.add(file);
            });
            inputImagen[0].files = dataTransfer.files;
        }
    });

    //Manejo de descripciones de las imagenes existentes al eliminar una imagen
    document.addEventListener('DOMContentLoaded', function() {
        const descriptionTextarea = document.getElementById('descripciones');
        const existingImagesContainer = document.querySelector('.existing-images-container'); // Reemplaza por el selector del contenedor de imágenes si es necesario

        if (!descriptionTextarea) return;

        // Función para actualizar el textarea al eliminar una imagen existente
        function updateDescriptionsOnDelete(imageElement) {
            // 1. Obtener el índice visual de la imagen a eliminar
            // Asumimos que el orden de las descripciones en el textarea coincide con el orden visual de las imágenes
            const images = Array.from(document.querySelectorAll('.existing-img:not(.d-none)'));
            const indexToDelete = images.indexOf(imageElement);
            
            if (indexToDelete === -1) return;

            // 2. Obtener todas las descripciones como un array de líneas
            let descriptions = descriptionTextarea.value.split('\n');
            
            // 3. Eliminar el elemento en el índice encontrado
            if (indexToDelete < descriptions.length) {
                descriptions.splice(indexToDelete, 1);
            }

            // 4. Actualizar el textarea con las líneas restantes
            descriptionTextarea.value = descriptions.join('\n');
        }

        // Escuchador de eventos para el botón de eliminación
        document.querySelectorAll('.delete-existing-img').forEach(button => {
            button.addEventListener('click', function() {
                const imageElement = this.closest('.existing-img');
                
                // 1. Ocultar la imagen (esto ya lo haces, probablemente)
                // imageElement.classList.add('d-none'); 

                // 2. Ejecutar la sincronización del textarea
                updateDescriptionsOnDelete(imageElement);

                // 3. (Tu lógica para añadir el ID a la lista de eliminados si aplica)
                // const imageId = imageElement.dataset.id;
                // // ... añadir imageId a un campo oculto de eliminados
            });
        });
    });





    // ---------------------------------------------------------------------------------------------
    // ===== DOCUMENTOS - RESTRICCIÓN DE TAMAÑO =====================================================
    // ---------------------------------------------------------------------------------------------

    // Si estamos editando una publicacion, manejamos la ELIMINACION de documentos existentes-----
        const docsAEliminar = [];
        const hiddenDocsAEliminar = $('#documentos_a_eliminar');
        const docPreviewContainer = $('#docPreviewContainer');

        // Inicializa el campo oculto con el array vacío
        hiddenDocsAEliminar.val(JSON.stringify(docsAEliminar));

        // 1. Manejar la eliminación de documentos existentes
        docPreviewContainer.on('click', '.delete-existing-doc', function() {
            const item = $(this).closest('.existing-doc');
            const compositeKey = item.data('id');

            if (confirm('¿Estás seguro de que deseas eliminar este documento?')) {
                // Añadir el ID al array de eliminación
                docsAEliminar.push(compositeKey);
                
                // Actualizar el campo oculto (JSON)
                hiddenDocsAEliminar.val(JSON.stringify(docsAEliminar));
                
                // Ocultar el elemento visual
                item.remove();
            }
        });
    // ---------------------------------------------------------------------------------------------

    // Lógica para manejar la selección de nuevos documentos con restricción de tamaño
    const MAX_SIZE_BYTES = 2097152; // 2 MB (2 * 1024 * 1024)
    const inputDoc = $('#inputGroupFileDoc');
    const fileMap = new Map();
    let fileCounter = 0;

    inputDoc.on('change', function() {
        const files = inputDoc[0].files;
        if (!files.length) return;

        let filesToProcess = [];
        let filesRejected = [];

        // 1. ITERAR y VALIDAR el tamaño de cada archivo
        Array.from(files).forEach((file) => {
            if (file.size > MAX_SIZE_BYTES) {
                filesRejected.push(file.name);
            } else {
                filesToProcess.push(file);
            }
        });
        
        // 2. MOSTRAR MENSAJES DE ERROR (si hay archivos rechazados)
        if (filesRejected.length > 0) {
            alert('Los siguientes archivos superan el límite de 2MB y no han sido añadidos:\n- ' + filesRejected.join('\n- '));
        }

        // 3. AÑADIR SOLO LOS ARCHIVOS VÁLIDOS A LA PREVISUALIZACIÓN Y AL MAPA
        filesToProcess.forEach((file) => {
            const fileId = fileCounter++;
            fileMap.set(fileId, file);
            
            // --- Lógica de Iconos (Tu lógica original) ---
            const ext = file.name.split('.').pop().toLowerCase();
            let iconClass = 'fa-file';
            let colorClass = 'text-secondary';

            switch (ext) {
                case 'pdf': iconClass = 'fa-file-pdf'; colorClass = 'text-danger'; break;
                case 'doc':
                case 'docx': iconClass = 'fa-file-word'; colorClass = 'text-primary'; break;
                // ... (otros casos de iconos) ...
                case 'txt': iconClass = 'fa-file-lines'; colorClass = 'text-info'; break;
            }

            // Crea el elemento visual
            const item = $(`
                <div id="doc-item-${fileId}" class="d-flex align-items-center mb-1 border p-2 rounded" style="max-width: 400px;">
                    <i class="fa ${iconClass} me-2 ${colorClass}" style="font-size: 1.3rem;"></i>
                    <span class="flex-grow-1 text-truncate">${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)</span>
                    <button type="button" class="btn btn-sm btn-outline-danger ms-2" data-file-id="${fileId}">&times;</button>
                </div>
            `);

            // Lógica para eliminar del listado y del mapa
            item.find('button').on('click', function() {
                const idToRemove = $(this).data('file-id');
                fileMap.delete(idToRemove);
                item.remove();
                updateFileInput(); 
            });

            docPreviewContainer.append(item);
        });

        // 4. Actualizar el FileList con solo los archivos válidos
        updateFileInput();
        
        // Función para actualizar el input file con los archivos válidos restantes
        function updateFileInput() {
            const dataTransfer = new DataTransfer();
            fileMap.forEach(file => {
                dataTransfer.items.add(file);
            });
            inputDoc[0].files = dataTransfer.files;
        }
    });

    /*const documentos = [];
    const inputDoc = $('#inputGroupFileDoc');
    const addDocBtn = $('#inputGroupFileAddonDoc');
    const docPreviewContainer = $('#docPreviewContainer');
    const hiddenDocumentos = $('#documentos_json');

    addDocBtn.on('click', function() {
        const files = inputDoc[0].files;
        if (!files.length) return;

        Array.from(files).forEach((file) => {
            const ext = file.name.split('.').pop().toLowerCase();

            // Determina el icono según la extensión
            let iconClass = 'fa-file';
            let colorClass = 'text-secondary';

            switch (ext) {
                case 'pdf':
                    iconClass = 'fa-file-pdf'; colorClass = 'text-danger'; break;
                case 'doc':
                case 'docx':
                    iconClass = 'fa-file-word'; colorClass = 'text-primary'; break;
                case 'xls':
                case 'xlsx':
                    iconClass = 'fa-file-excel'; colorClass = 'text-success'; break;
                case 'ppt':
                case 'pptx':
                    iconClass = 'fa-file-powerpoint'; colorClass = 'text-warning'; break;
                case 'zip':
                case 'rar':
                    iconClass = 'fa-file-archive'; colorClass = 'text-muted'; break;
                case 'txt':
                    iconClass = 'fa-file-lines'; colorClass = 'text-info'; break;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                const base64 = e.target.result;
                const docObj = {
                    name: file.name,
                    data: base64,
                    size: file.size,
                    type: ext
                };
                documentos.push(docObj);

                // Crea el elemento visual
                const item = $(`
                    <div class="d-flex align-items-center mb-1 border p-2 rounded" style="max-width: 400px;">
                        <i class="fa ${iconClass} me-2 ${colorClass}" style="font-size: 1.3rem;"></i>
                        <span class="flex-grow-1 text-truncate">${file.name}</span>
                        <button type="button" class="btn btn-sm btn-outline-danger ms-2">&times;</button>
                    </div>
                `);

                // Eliminar documento del listado
                item.find('button').on('click', function() {
                    const index = docPreviewContainer.children().index(item);
                    documentos.splice(index, 1);
                    item.remove();
                    hiddenDocumentos.val(JSON.stringify(documentos));
                });

                docPreviewContainer.append(item);
                hiddenDocumentos.val(JSON.stringify(documentos));
            };
            reader.readAsDataURL(file);
        });

        inputDoc.val('');
    });*/


})()