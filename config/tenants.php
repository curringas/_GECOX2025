<?php

return [
    'default' => env('TENANT_DEFAULT', 'granadaesnoticia'),

    'tenants' => [
        'granadaesnoticia' => [
            'name'  => 'Granada Es Noticia',
            'hosts' => array_values(array_filter(array_map('trim', explode(',',
                (string) env('TENANT_ESNOTICIA_HOSTS', 'admin.granadaesnoticia.com'))))),
            'db' => [
                'host'     => env('TENANT_ESNOTICIA_DB_HOST', env('DB_HOST', '127.0.0.1')),
                'port'     => env('TENANT_ESNOTICIA_DB_PORT', env('DB_PORT', '3306')),
                'database' => env('TENANT_ESNOTICIA_DB_DATABASE', env('DB_DATABASE')),
                'username' => env('TENANT_ESNOTICIA_DB_USERNAME', env('DB_USERNAME')),
                'password' => env('TENANT_ESNOTICIA_DB_PASSWORD', env('DB_PASSWORD')),
            ],
            'storage' => '',
            'logo'    => 'granadaesnoticia',
            // URL publica del front y token para vaciar su cache (borrarcache.php).
            'public_url'        => env('TENANT_ESNOTICIA_PUBLIC_URL', 'https://www.granadaesnoticia.com'),
            'cache_purge_token' => env('TENANT_ESNOTICIA_CACHE_TOKEN'),
        ],

        'granadaenjuego' => [
            'name'  => 'Granada En Juego',
            'hosts' => array_values(array_filter(array_map('trim', explode(',',
                (string) env('TENANT_ENJUEGO_HOSTS', 'admin.granadaenjuego.com'))))),
            'db' => [
                'host'     => env('TENANT_ENJUEGO_DB_HOST', env('DB_HOST', '127.0.0.1')),
                'port'     => env('TENANT_ENJUEGO_DB_PORT', env('DB_PORT', '3306')),
                'database' => env('TENANT_ENJUEGO_DB_DATABASE'),
                'username' => env('TENANT_ENJUEGO_DB_USERNAME'),
                'password' => env('TENANT_ENJUEGO_DB_PASSWORD'),
            ],
            'storage' => 'tenants/granadaenjuego',
            'logo'    => 'granadaenjuego',
            // URL publica del front y token para vaciar su cache (borrarcache.php).
            'public_url'        => env('TENANT_ENJUEGO_PUBLIC_URL', 'https://www.granadaenjuego.com'),
            'cache_purge_token' => env('TENANT_ENJUEGO_CACHE_TOKEN'),
        ],
    ],
];
