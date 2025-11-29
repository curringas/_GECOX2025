<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentoEnPublicacion extends Model
{
    protected $table = 'P0114_documentoenpublicacion';
    public $timestamps = false;
    protected $primaryKey = null;
    public $incrementing = false;

    protected $fillable = [
        'Documento',
        'Tamano',
        'Nombre',
        'Publicacion',
        'Icono',
        'Orden',
    ];

    public function publicacion()
    {
        return $this->belongsTo(Publicacion::class, 'Publicacion', 'Identificador');
    }
}
