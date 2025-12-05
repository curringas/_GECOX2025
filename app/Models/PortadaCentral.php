<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PortadaCentral extends Model
{
    /**
     * Nombre de la tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'P0114_portada_central';

    /**
     * Indica si el modelo debe tener timestamps (created_at/updated_at).
     *
     * @var bool
     */
    public $timestamps = false;
    
    /**
     * La clave primaria de la tabla.
     *
     * @var string
     */
    protected $primaryKey = 'Identificador';

    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array
     */
    protected $fillable = [
        'Publicacion',
        'Imagen',
        'Automatico',
        'BannerImagen',
        'BannerTitulo',
        'BannerUrl',
        'BannerDestino',
        'BannerCodigoFuente',
        'Orden',
    ];
}