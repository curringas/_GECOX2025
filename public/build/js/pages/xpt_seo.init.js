/*
CURRO / XPERIMENTA - GECOX 2025 / PALABREA
*/

function cleanInputSlug(text) {
    if (!text) {
        return '';
    }

    return text.toString().toLowerCase()
        // 1. Normalizar y quitar acentos
        .normalize('NFD').replace(/[\u0300-\u036f]/g, "") 
        // 2. Reemplazar no-alfanuméricos/espacios por guiones
        .replace(/[^a-z0-9\s-]/g, "") 
        // 3. Reemplazar espacios y guiones múltiples por un solo guion
        .trim()
        .replace(/[\s-]+/g, '-'); 
        // *** IMPORTANTE: NO SE ELIMINAN LOS GUIONES EN LOS EXTREMOS ***
}

// Limpieza 'Final' para la AUTOGENERACIÓN
// (Es la que usarás para el Título -> Slug)
function finalizeSlug(text) {
    // Usa la función suave, y luego remueve los guiones de los extremos
    return cleanInputSlug(text).replace(/^-+|-+$/g, '');
}
// ----------------------------------------------------

(function () {
    'use strict';

    // ++++++++++++++++++ MENEJO DEL SLUG +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    /**
     * Convierte una cadena de texto a un formato 'slug' limpio y URL-friendly.
     * (Misma lógica pura de JS, no necesita jQuery)
     */
    

    /**
     * Configura la lógica de autogeneración y validación del slug usando jQuery.
     * @param {string} sourceSelector - Selector de origen (e.g., '#Titulo', '#Etiqueta').
     * @param {string} targetSelector - Selector de destino (el slug, e.g., '#Url', '#UrlCategoria').
     */
    window.setupSlugGenerator = function(sourceSelector, targetSelector) {
    const $sourceInput = $(sourceSelector); 
    const $targetInput = $(targetSelector); 

    if ($sourceInput.length === 0 || $targetInput.length === 0) {
        console.warn(`Inputs no encontrados para la configuración del slug: Origen='${sourceSelector}', Destino='${targetSelector}'`);
        return;
    }

    // Bandera: TRUE si el campo de SLUG ya tiene contenido (caso de edición con slug existente)
    let initialUrlValue = $targetInput.attr('value'); 
    //console.log("setupSlugGenerator inicializado. initialUrlValue='" + initialUrlValue + "'");

    // Utilizamos el valor del atributo para la bandera.
    let isSlugManuallyEdited = initialUrlValue && initialUrlValue.length > 0;

    // --- 1. Generación Automática (Input en Título) ---
    // Usamos 'input' para que se haga en tiempo real
    $sourceInput.on('input', function() {
        if (!isSlugManuallyEdited) {
            // Usamos la limpieza FINAL (finalizeSlug) para la autogeneración.
            const slug = finalizeSlug($(this).val()); 
            $targetInput.val(slug);
        }
    });

    // --- 2. Control de Edición Manual ---
    $targetInput.on('focus', function() {
        // Al hacer focus, deshabilitamos permanentemente la autogeneración
        isSlugManuallyEdited = true;
    });
    
    // --- 3. Limpieza en Vivo (Edición Manual) ---
    $targetInput.on('input', function() {
        // Opcional: Si el usuario borra todo el slug y aún no ha hecho focus, 
        // reactivamos temporalmente la autogeneración.
        if ($(this).val() === '' && $sourceInput.val().length > 0) {
             isSlugManuallyEdited = false;
        }

        // Usamos la limpieza SUAVE (cleanInputSlug) para permitir que el guion se escriba.
        const cleanedValue = cleanInputSlug($(this).val());
        $(this).val(cleanedValue);
    });

    // --- 4. Generación Inicial al Cargar (Si hay título y no hay slug) ---
    if ($targetInput.val() === '' && $sourceInput.val() !== '') {
        // Usamos la limpieza FINAL (finalizeSlug) para la inicialización
        const initialSlug = finalizeSlug($sourceInput.val());
        $targetInput.val(initialSlug);
    }
}

    // ++++++++++++++++++ MENEJO META TITLE Y META DESCRIPTION +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

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
