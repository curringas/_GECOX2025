<?php

return [
    /*
     * Modo mantenimiento PROPIO (distinto del `php artisan down` nativo):
     * cuando 'activo' = true, el público ve la vista mantenimiento.maintenance (503)
     * y solo pasan las IPs listadas en 'ips'. Se controla desde el .env.
     *
     * La IP real del cliente se lee dentro del middleware (X-Forwarded-For si
     * viene del proxy de Plesk, o REMOTE_ADDR si no), sin tocar la config global
     * de TrustProxies.
     */
    'activo' => (bool) env('MODO_MANTENIMIENTO', false),

    // IPs permitidas durante el mantenimiento (coma-separadas, sin puerto).
    'ips' => array_values(array_filter(array_map('trim', explode(',',
        (string) env('IPS_PERMITIDAS_EN_MANTENIMIENTO', ''))))),
];
