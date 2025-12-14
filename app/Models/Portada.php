<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Portada extends Model
{
    // 1. Nombre de la tabla
    protected $table = 'P0114_portada';

    // 2. Deshabilitar timestamps (created_at y updated_at)
    // Ya que la tabla no tiene estas columnas
    public $timestamps = false;
    
    // 3. Clave Primaria (aunque no sea auto-incremental)
    // Laravel necesita saber qué columna usar para buscar/actualizar.
    // Usaremos 'Ticker' como clave primaria si realmente necesitas usar
    // métodos como find() o save() en una instancia específica,
    // o puedes establecerla como nula si solo harás 'update' o 'insert' directo.
    // Si la vas a usar como clave, aunque sea un campo de texto:
    protected $primaryKey = 'Ticker';
    
    // 4. Indica que la clave primaria no es auto-incremental
    public $incrementing = false;
    
    // 5. Tipo de la clave primaria (varchar en tu caso)
    protected $keyType = 'string';

    // 6. Campos que se pueden asignar masivamente (Mass Assignment)
    // Se listan todas las columnas de tu tabla
    protected $fillable = [
        'Ticker',
        'Titulo',
        'Contenido',
        'Foto',
        'Principal',
        'banner_cabeceraTitulo',
        'banner_cabeceraImagen',
        'banner_cabeceraUrl',
        'banner_cabeceraDestino',
        'banner_cabeceraCodigoFuente',
        'banner_izquierdaTitulo',
        'banner_izquierdaImagen',
        'banner_izquierdaUrl',
        'banner_izquierdaDestino',
        'banner_izquierdaCodigoFuente',
        'banner_derechaTitulo',
        'banner_derechaImagen',
        'banner_derechaUrl',
        'banner_derechaDestino',
        'banner_derechaCodigoFuente',
        'Intersticial_imagen',
        'Intersticial_link',
        'banner_pieTitulo',
        'banner_pieImagen',
        'banner_pieUrl',
        'banner_pieDestino',
        'banner_pieCodigoFuente',
        'banner_cabeceraImagenMovil',
        'banner_pieImagenMovil',
        'banner_izquierdaImagenMovil',
        'banner_derechaImagenMovil',
        'Intersticial_imagenMovil'
    ];

    // Opcionalmente, si prefieres no listar todos los campos, puedes usar $guarded
    // para indicar qué campos NO pueden ser asignados masivamente. 
    // Si pones array vacío, todos son asignables (usar con precaución).
    // protected $guarded = [];

    // 7. Casts (Tipado de datos)
    // 'Principal' es un INT, podemos asegurarnos de que se maneje como un entero.
    protected $casts = [
        'Principal' => 'integer',
    ];
}