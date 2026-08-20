<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckMaintenanceMode
{
    /**
     * Modo mantenimiento propio con lista de IPs permitidas.
     * Se activa con MODO_MANTENIMIENTO=true en el .env; las IPs que pueden
     * seguir usando el panel van en IPS_PERMITIDAS_EN_MANTENIMIENTO.
     * Ver config/mantenimiento.php.
     */
    public function handle(Request $request, Closure $next)
    {
        if (config('mantenimiento.activo') && ! in_array($request->ip(), config('mantenimiento.ips', []), true)) {
            return response()->view('mantenimiento.maintenance', [], 503);
        }

        return $next($request);
    }
}
