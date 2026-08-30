<?php

namespace App\Support;

use Carbon\Carbon;

/**
 * Lógica pura de programación de visibilidad de una publicación.
 *
 * Dadas las fechas de Activacion/Desactivacion y el momento actual,
 * decide el estado que debe tener la noticia (Activa) y qué fechas
 * quedan pendientes tras aplicar la programación.
 *
 * Es idempotente: puede ejecutarse repetidamente sin efectos raros.
 * Solo tiene sentido para filas con al menos una fecha; las filas sin
 * fechas se consideran de activación manual y no se procesan.
 */
class ProgramacionPublicacion
{
    /**
     * @return array{Activa:int, Activacion:?Carbon, Desactivacion:?Carbon}
     */
    public static function estado(Carbon $ahora, ?Carbon $activacion, ?Carbon $desactivacion): array
    {
        // La desactivación manda: si ya pasó, se apaga y la programación
        // se considera consumida (se limpian ambas fechas).
        if ($desactivacion !== null && $ahora->greaterThanOrEqualTo($desactivacion)) {
            return ['Activa' => 0, 'Activacion' => null, 'Desactivacion' => null];
        }

        // Activación ya pasada: se enciende y se consume la Activacion.
        // Se conserva la Desactivacion pendiente (que aquí es futura).
        if ($activacion !== null && $ahora->greaterThanOrEqualTo($activacion)) {
            return ['Activa' => 1, 'Activacion' => null, 'Desactivacion' => $desactivacion];
        }

        // Activación futura: aún no se publica.
        if ($activacion !== null) {
            return ['Activa' => 0, 'Activacion' => $activacion, 'Desactivacion' => $desactivacion];
        }

        // Solo queda una desactivación futura: visible hasta que llegue.
        return ['Activa' => 1, 'Activacion' => null, 'Desactivacion' => $desactivacion];
    }
}
