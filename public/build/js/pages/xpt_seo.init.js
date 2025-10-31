/*
CURRO / XPERIMENTA - GECOX 2025 / PALABREA
*/

(function () {
    'use strict';

    function actualizarCamposPorTipo() {
        const tipo = parseInt($('#tipo').val());

        // Ocultar todos los grupos dependientes
       // Primero mostrar todo por defecto
        $('.hideable-link-external').hide();
        $('.hideable-estatico').hide();
        $('.hideable-solo-etiqueta').show();

        // Mostrar según tipo
        if (tipo === 1) {
            $('.hideable-estatico').show();
        } else if (tipo === 3) {
            $('.hideable-solo-etiqueta').hide();
            $('.hideable-link-external').show();
        } else if (tipo === 2) {
            $('.hideable-solo-etiqueta').hide(); // Si más adelante hay secciones especiales para etiquetas
        }
        // tipo 0 → no se hace nada
    }

    // Llamar al cargar (para mantener coherencia al editar)
    actualizarCamposPorTipo();

    // Llamar cada vez que se cambia el tipo
    $('#tipo').on('change', function() {
        actualizarCamposPorTipo();
    });

    $('#formCategoria').on('submit', function() {
        // Antes de enviar el formulario, asegurarse de que los campos ocultos no se envían
        $('.hideable-link-external input, .hideable-estatico input').each(function() {
            if ($(this).closest('.hideable-link-external, .hideable-estatico').is(':hidden')) {
                $(this).val(''); // Limpiar el valor para que no se envíe
            }
        });
    });


    // Inicializar TinyMCE para el textarea explicativo WYSYIWYG
    tinymce.init({
        selector: 'textarea#explicativo, textarea#explicativoproductos',
        height: 350,
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



    document.addEventListener('DOMContentLoaded', function() {
        const titleInput = document.getElementById('MetaTitle');
        const descInput = document.getElementById('MetaDescription');
        const titleCount = document.getElementById('titleCount');
        const descCount = document.getElementById('descCount');

        // Función genérica para actualizar contador
        function updateCounter(input, counter, max) {
            if (!input || !counter) return; // evita errores
            const length = input.value.trim().length;
            counter.textContent = `${length} / ${max}`;
            counter.classList.toggle('text-danger', length > max);
        }

        // Inicializa y escucha cambios solo si existen los campos
        if (titleInput && titleCount) {
            titleInput.addEventListener('input', () => updateCounter(titleInput, titleCount, 70));
            updateCounter(titleInput, titleCount, 70);
        }

        if (descInput && descCount) {
            descInput.addEventListener('input', () => updateCounter(descInput, descCount, 170));
            updateCounter(descInput, descCount, 170);
        }
    });
})();
