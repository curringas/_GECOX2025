<?php

return [
    'default' => env('TENANT_DEFAULT', 'granadaesnoticia'),

    'tenants' => [
        'granadaesnoticia' => [
            'name'  => 'Granada Es Noticia',
            'hosts' => ['admin.granadaesnoticia.com', 'granadaesnoticia.test'],
            'db' => [
                'host'     => env('TENANT_ESNOTICIA_DB_HOST', '127.0.0.1'),
                'port'     => env('TENANT_ESNOTICIA_DB_PORT', '3306'),
                'database' => env('TENANT_ESNOTICIA_DB_DATABASE', env('DB_DATABASE')),
                'username' => env('TENANT_ESNOTICIA_DB_USERNAME', env('DB_USERNAME')),
                'password' => env('TENANT_ESNOTICIA_DB_PASSWORD', env('DB_PASSWORD')),
            ],
            'storage' => '',
            'logo'    => 'granadaesnoticia',
        ],

        'granadaenjuego' => [
            'name'  => 'Granada En Juego',
            'hosts' => ['admin.granadaenjuego.com', 'granadaenjuego.test'],
            'db' => [
                'host'     => env('TENANT_ENJUEGO_DB_HOST', '127.0.0.1'),
                'port'     => env('TENANT_ENJUEGO_DB_PORT', '3306'),
                'database' => env('TENANT_ENJUEGO_DB_DATABASE'),
                'username' => env('TENANT_ENJUEGO_DB_USERNAME'),
                'password' => env('TENANT_ENJUEGO_DB_PASSWORD'),
            ],
            'storage' => 'tenants/granadaenjuego',
            'logo'    => 'granadaenjuego',
        ],
    ],
];
