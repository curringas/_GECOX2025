<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenanceMode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $allowedIps = explode(',', env('IPS_PERMITIDAS_EN_MANTENIMIENTO', ''));

        // Puedes usar una variable de entorno también si quieres:
        // $allowedIp = env('MY_DEV_IP', '127.0.0.1');
        // dd($request->ip(), $allowedIps);
        if (!in_array($request->ip(), $allowedIps)) {
            return response()->view('mantenimiento.maintenance'); // Vista personalizada
        }

        return $next($request);
    }
}
