<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Publicacion extends Model
{
    protected $table = 'P0114_publicacion';

    protected $primaryKey = 'Identificador';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'Creador', 'Fecha', 'Privacidad', 'Activa', 'Pendiente', 'Url', 'Email', 'Titulo',
        'Subtitulo', 'Contenido', 'TextoApoyo', 'ContenidoCompleto', 'IconoImprimir',
        'IconoEnviar', 'IconoRelacionados', 'IconoValorar', 'Activacion', 'Desactivacion',
        'Notas', 'Introduccion', 'FechaInicio', 'FechaFin', 'FechaSalida', 'Autor', 'Lugar',
        'Logotipo', 'LugarTipo', 'Video', 'LlevaComentarios', 'GaleriaURL', 'Keywords',
        'Visitas', 'AutorTwitter', 'AutorEmail',
    ];
}
