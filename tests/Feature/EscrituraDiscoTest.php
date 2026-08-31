<?php

namespace Tests\Feature;

use App\Support\EscrituraDisco;
use Illuminate\Support\Facades\Storage;
use Mockery;
use RuntimeException;
use Tests\TestCase;

/**
 * EscrituraDisco::guardar debe fallar RUIDOSAMENTE si el disco no puede
 * escribir (Storage::put devuelve false, p. ej. por permisos/propietario),
 * en lugar de continuar en silencio y dejar registros huérfanos.
 */
class EscrituraDiscoTest extends TestCase
{
    public function test_guarda_cuando_el_disco_puede_escribir(): void
    {
        Storage::fake('public');

        EscrituraDisco::guardar('public', 'ficheros/202608/imagen.jpg', 'contenido-binario');

        Storage::disk('public')->assertExists('ficheros/202608/imagen.jpg');
    }

    public function test_lanza_excepcion_si_el_disco_no_puede_escribir(): void
    {
        $disco = Mockery::mock();
        $disco->shouldReceive('put')->once()->andReturn(false);
        Storage::shouldReceive('disk')->with('public')->andReturn($disco);

        $this->expectException(RuntimeException::class);

        EscrituraDisco::guardar('public', 'ficheros/202608/imagen.jpg', 'contenido-binario');
    }
}
