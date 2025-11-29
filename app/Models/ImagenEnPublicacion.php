<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImagenEnPublicacion extends Model
{
    protected $table = 'P0114_imagenenpublicacion';
    public $timestamps = false;
    protected $primaryKey = null;
    public $incrementing = false;

    protected $fillable = [
        'Imagen',
        'Descripcion',
        'Ancho',
        'Publicacion',
        'Orden',
        'Repositorio',
    ];

    public function publicacion()
    {
        return $this->belongsTo(Publicacion::class, 'Publicacion', 'Identificador');
    }
}