/*
CURRO / XPERIMENTA - GECOX 2025 / PALABREA
*/

(function () {
    'use strict';

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
