<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;
use RuntimeException;

/**
 * Escritura en disco que falla de forma visible.
 *
 * Storage::put() devuelve false (sin lanzar excepción) cuando no puede escribir
 * —por ejemplo si el directorio de storage tiene otro propietario/permisos—.
 * Ignorar ese false deja registros en BD que apuntan a ficheros inexistentes
 * (imágenes fantasma). Este ayudante lo convierte en un error explícito.
 */
class EscrituraDisco
{
    public static function guardar(string $disco, string $ruta, string $contenido): void
    {
        if (Storage::disk($disco)->put($ruta, $contenido) === false) {
            throw new RuntimeException(
                "No se pudo escribir '{$ruta}' en el disco '{$disco}'. "
                ."Revisa los permisos y el propietario del directorio de storage del tenant."
            );
        }
    }
}
