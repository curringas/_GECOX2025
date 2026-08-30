<?php

namespace Tests\Unit;

use App\Support\ProgramacionPublicacion;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class ProgramacionPublicacionTest extends TestCase
{
    private Carbon $ahora;

    protected function setUp(): void
    {
        parent::setUp();
        $this->ahora = Carbon::create(2026, 8, 30, 12, 0, 0);
    }

    public function test_activacion_pasada_enciende_y_limpia_activacion_manteniendo_desactivacion_futura(): void
    {
        $estado = ProgramacionPublicacion::estado(
            $this->ahora,
            $this->ahora->copy()->subHour(),        // Activacion ya pasada
            $this->ahora->copy()->addDay()          // Desactivacion futura
        );

        $this->assertSame(1, $estado['Activa']);
        $this->assertNull($estado['Activacion']);
        $this->assertNotNull($estado['Desactivacion']);
    }

    public function test_activacion_futura_mantiene_oculta_y_conserva_fechas(): void
    {
        $estado = ProgramacionPublicacion::estado(
            $this->ahora,
            $this->ahora->copy()->addDay(),         // Activacion futura
            $this->ahora->copy()->addDays(2)        // Desactivacion futura
        );

        $this->assertSame(0, $estado['Activa']);
        $this->assertNotNull($estado['Activacion']);
        $this->assertNotNull($estado['Desactivacion']);
    }

    public function test_desactivacion_pasada_apaga_y_limpia_ambas_fechas(): void
    {
        $estado = ProgramacionPublicacion::estado(
            $this->ahora,
            $this->ahora->copy()->subDays(2),       // Activacion pasada
            $this->ahora->copy()->subHour()         // Desactivacion pasada
        );

        $this->assertSame(0, $estado['Activa']);
        $this->assertNull($estado['Activacion']);
        $this->assertNull($estado['Desactivacion']);
    }

    public function test_solo_desactivacion_futura_deja_visible(): void
    {
        $estado = ProgramacionPublicacion::estado(
            $this->ahora,
            null,                                    // sin Activacion
            $this->ahora->copy()->addDay()          // Desactivacion futura
        );

        $this->assertSame(1, $estado['Activa']);
        $this->assertNull($estado['Activacion']);
        $this->assertNotNull($estado['Desactivacion']);
    }

    public function test_solo_activacion_pasada_enciende_y_queda_sin_fechas(): void
    {
        $estado = ProgramacionPublicacion::estado(
            $this->ahora,
            $this->ahora->copy()->subHour(),        // Activacion pasada
            null                                     // sin Desactivacion
        );

        $this->assertSame(1, $estado['Activa']);
        $this->assertNull($estado['Activacion']);
        $this->assertNull($estado['Desactivacion']);
    }
}
