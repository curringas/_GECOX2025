<?php

namespace Tests\Feature;

use App\Http\Requests\PublicacionRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

/**
 * Validación de la programación de visibilidad en el formulario de publicación.
 * No toca BD: solo comprueba el conjunto de reglas del PublicacionRequest.
 */
class PublicacionProgramacionValidacionTest extends TestCase
{
    private function validar(array $datos): \Illuminate\Contracts\Validation\Validator
    {
        // Simula lo que hace prepareForValidation: las fechas vacías llegan como null.
        $datos += ['Activacion' => null, 'Desactivacion' => null];

        $request = PublicacionRequest::create('/', 'POST', $datos);
        $reglas = $request->rules();

        return Validator::make(
            $datos,
            ['Activacion' => $reglas['Activacion'], 'Desactivacion' => $reglas['Desactivacion']]
        );
    }

    public function test_automatica_solo_con_activacion_es_valido(): void
    {
        $v = $this->validar([
            'ModoActivacion' => 'automatica',
            'Activacion' => '2026-08-30 19:50:00',
        ]);

        $this->assertFalse($v->fails(), 'Dejar la desactivación vacía debe ser válido: '.$v->errors());
    }

    public function test_automatica_solo_con_desactivacion_es_valido(): void
    {
        $v = $this->validar([
            'ModoActivacion' => 'automatica',
            'Desactivacion' => '2026-08-30 19:50:00',
        ]);

        $this->assertFalse($v->fails(), 'Dejar la activación vacía debe ser válido: '.$v->errors());
    }

    public function test_automatica_sin_ninguna_fecha_falla(): void
    {
        $v = $this->validar(['ModoActivacion' => 'automatica']);

        $this->assertTrue($v->fails());
        $this->assertTrue($v->errors()->has('Activacion'));
        $this->assertTrue($v->errors()->has('Desactivacion'));
    }

    public function test_automatica_con_desactivacion_anterior_a_activacion_falla(): void
    {
        $v = $this->validar([
            'ModoActivacion' => 'automatica',
            'Activacion' => '2026-08-30 19:50:00',
            'Desactivacion' => '2026-08-29 10:00:00',
        ]);

        $this->assertTrue($v->fails());
        $this->assertTrue($v->errors()->has('Desactivacion'));
    }
}
