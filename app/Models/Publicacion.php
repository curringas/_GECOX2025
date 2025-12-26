<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Categoria;
use App\Models\ImagenEnPublicacion;
use App\Models\DocumentoEnPublicacion;

class Publicacion extends Model
{
    protected $table = 'P0114_publicacion';

    protected $primaryKey = 'Identificador';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $guarded = [
        'Identificador',
        'Visitas', 
        'Creador',
    ];

    protected $fillable = [
        'Creador', 'Fecha', 'Privacidad', 'Activa', 'Pendiente', 'Url', 'Email', 'Titulo',
        'Subtitulo', 'Contenido', 'TextoApoyo', 'ContenidoCompleto', 'IconoImprimir',
        'IconoEnviar', 'IconoRelacionados', 'IconoValorar', 'Activacion', 'Desactivacion',
        'Notas', 'Introduccion', 'FechaInicio', 'FechaFin', 'FechaSalida', 'Autor', 'Lugar',
        'Logotipo', 'LugarTipo', 'Video', 'LlevaComentarios', 'GaleriaURL', 'Keywords',
        'Visitas', 'AutorTwitter', 'AutorEmail','MetaTitle', 'MetaDescription',
    ];

    protected $casts = [
        'Fecha' => 'datetime',
    ];

    public function categorias()
    {
        return $this->belongsToMany(
            Categoria::class,           // Modelo relacionado
            'P0114_publicacionpagina',      // Nombre de la tabla pivote
            'Publicacion',            // Clave foránea de este modelo
            'Pagina'              // Clave foránea del otro modelo
        )->withPivot('Orden','Ultima');
    }

    public function imagenes()
    {
        return $this->hasMany(ImagenEnPublicacion::class, 'Publicacion', 'Identificador')->orderBy('Orden');
    }

    public function documentos()
    {
        return $this->hasMany(DocumentoEnPublicacion::class, 'Publicacion', 'Identificador')->orderBy('Orden');
    }

}
