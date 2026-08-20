<?php

return [
    /*
     * Modo mantenimiento PROPIO (distinto del `php artisan down` nativo):
     * cuando 'activo' = true, el público ve la vista mantenimiento.maintenance (503)
     * y solo pasan las IPs listadas en 'ips'. Se controla desde el .env.
     *
     * OJO: para que la IP del cliente se detecte bien detrás del proxy de Plesk,
     * TrustProxies debe confiar en el proxy (ver app/Http/Middleware/TrustProxies.php).
     */
    'activo' => (bool) env('MODO_MANTENIMIENTO', false),

    // IPs permitidas durante el mantenimiento (coma-separadas, sin puerto).
    'ips' => array_values(array_filter(array_map('trim', explode(',',
        (string) env('IPS_PERMITIDAS_EN_MANTENIMIENTO', ''))))),
];
