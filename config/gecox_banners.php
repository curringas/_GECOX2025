<?php

return [
    'targets' => [
        0 => '_self',
        1 => '_blank',
    ],
    'tipos' => [
        0 => 'Pequeño',
        1 => 'Mediano',
        2 => 'Grande',
    ],
    'sizes' => [
        'pequeno' => [
            'ancho' => 980,
            'alto' => 120,
        ],
        'mediano' => [
            'ancho' => 310,
            'alto' => 300,
        ],
        'grande' => [
            'ancho' => 150,
            'alto' => 150,
        ],
    ],
    'portada' => [
        'ancho_cabecera' => 728,
        'alto_cabecera' => 90,
        'ancho_izquierda' => 627,
        'alto_izquierda' => null,
        'ancho_derecha' => 375,
        'alto_derecha' => null,
    ],
    'pesos' => [
        'pequeno' => 150,    // Peso máximo en KB para banners pequeños
        'mediano' => 190,  // Peso máximo en KB para banners medianos
        'grande' => 400,   // Peso máximo en KB para banners grandes
    ],
    // Puedes añadir otras configuraciones relacionadas con banners aquí, como categorías por defecto, etc.
];