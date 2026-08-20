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
        if (config('mantenimiento.activo') && ! in_array($this->clientIp($request), config('mantenimiento.ips', []), true)) {
            return response()->view('mantenimiento.maintenance', [], 503);
        }

        return $next($request);
    }

    /**
     * IP real del cliente. Detrás del proxy de Plesk la IP del visitante viaja
     * en X-Forwarded-For (la primera de la lista); si no hay proxy, REMOTE_ADDR.
     * Se lee aquí para no tener que cambiar la config global de TrustProxies.
     */
    private function clientIp(Request $request): string
    {
        $xff = $request->headers->get('X-Forwarded-For');
        if ($xff) {
            return trim(explode(',', $xff)[0]);
        }

        return (string) $request->ip();
    }
}
