<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    // Nombre real de la tabla
    protected $table = 'P0114_pagina';

    // Clave primaria personalizada
    protected $primaryKey = 'Identificador';

    // Indica si la clave es autoincremental
    public $incrementing = true;

    // Tipo de clave primaria
    protected $keyType = 'int';

    // Sin timestamps automáticos
    public $timestamps = false;

    // Campos asignables en masa
    protected $fillable = [
        'Etiqueta',
        'Titulo',
        'Explicativo',
        'Boliche',
        'Menu',
        'Privacidad',
        'Cabecera',
        'Estatico',
        'Externo',
        'SoloEtiqueta',
        'Target',
        'Creador',
        'Fecha',
        'Orden',
        'Padre',
        'Bloques',
        'Visitas',
        'ExplicativoProductos',
        'Url',
        'MetaTitle',
        'MetaDescription',
    ];

    // Casts de tipos de datos
    protected $casts = [
        'SoloEtiqueta' => 'boolean',
        'Target' => 'boolean',
        'Menu' => 'integer',
        'Privacidad' => 'integer',
        'Cabecera' => 'integer',
        'Orden' => 'integer',
        'Padre' => 'integer',
        'Bloques' => 'integer',
        'Visitas' => 'integer',
        'Fecha' => 'date',
    ];

    // Ejemplo de relación: páginas hijas (subpáginas)
    public function hijos()
    {
        return $this->hasMany(self::class, 'Padre', 'Identificador')->orderBy('Orden');
    }

    // Relación inversa: página padre
    public function padre()
    {
        return $this->belongsTo(self::class, 'Padre', 'Identificador');
    }
}
