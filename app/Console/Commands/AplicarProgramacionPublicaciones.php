<?php

namespace App\Console\Commands;

use App\Models\Publicacion;
use App\Support\ProgramacionPublicacion;
use App\Tenancy\TenantManager;
use Carbon\Carbon;
use Illuminate\Console\Command;

/**
 * Aplica la programación de visibilidad de las publicaciones en TODOS los
 * tenants: enciende/apaga la columna Activa según las fechas Activacion y
 * Desactivacion, y limpia las fechas ya consumidas.
 *
 * Pensado para ejecutarse cada minuto desde el scheduler de Laravel
 * (ver App\Console\Kernel). La lógica por fila está en ProgramacionPublicacion
 * y es idempotente.
 */
class AplicarProgramacionPublicaciones extends Command
{
    protected $signature = 'publicaciones:aplicar-programacion';

    protected $description = 'Activa/desactiva publicaciones según sus fechas de Activacion/Desactivacion (todos los tenants)';

    public function handle(TenantManager $manager): int
    {
        $ahora = Carbon::now();
        $tenants = array_keys(config('tenants.tenants', []));

        foreach ($tenants as $slug) {
            $manager->configure($slug);

            $publicaciones = Publicacion::whereNotNull('Activacion')
                ->orWhereNotNull('Desactivacion')
                ->get(['Identificador', 'Activa', 'Activacion', 'Desactivacion']);

            $cambios = 0;

            foreach ($publicaciones as $pub) {
                $estado = ProgramacionPublicacion::estado(
                    $ahora->copy(),
                    $pub->Activacion ? Carbon::parse($pub->Activacion) : null,
                    $pub->Desactivacion ? Carbon::parse($pub->Desactivacion) : null,
                );

                $nuevaActivacion = $estado['Activacion']?->format('Y-m-d H:i:s');
                $nuevaDesactivacion = $estado['Desactivacion']?->format('Y-m-d H:i:s');

                $sinCambios = (int) $pub->Activa === $estado['Activa']
                    && (string) $pub->Activacion === (string) $nuevaActivacion
                    && (string) $pub->Desactivacion === (string) $nuevaDesactivacion;

                if ($sinCambios) {
                    continue;
                }

                $pub->Activa = $estado['Activa'];
                $pub->Activacion = $nuevaActivacion;
                $pub->Desactivacion = $nuevaDesactivacion;
                $pub->save();
                $cambios++;
            }

            $this->info("[{$slug}] Publicaciones actualizadas: {$cambios}");
        }

        return self::SUCCESS;
    }
}
