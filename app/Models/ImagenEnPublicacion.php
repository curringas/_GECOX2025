<?php
namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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

    public function getThumbUrlAttribute()
    {
        $ruta = $this->Imagen; // El valor original de la BD

        // CASO 1: Formato antiguo (sin carpetas)
        // Detectamos si NO tiene barras "/"
        if (strpos($ruta, '/') === false) {
            // Necesitamos la fecha de la publicación padre.
            // Usamos el operador nullsafe (?) por si la imagen quedó huérfana sin publicación
            $fechaPublicacion = $this->publicacion?->Fecha;

            if ($fechaPublicacion) {
                $carpetaFecha = Carbon::parse($fechaPublicacion)->format('Ym');
                $ruta = "ficheros/{$carpetaFecha}/{$ruta}";
            } else {
                // Fallback por si no hay publicación asociada: 
                // Devuelve una imagen por defecto o la ruta tal cual
                return Storage::disk('public')->url('img/default_thumb.jpg');
            }
        }

        // CASO 2: Procesamiento del thumbnail (común para ambos)
        $info = pathinfo($ruta);
        $dirname = $info['dirname'];
        $filename = $info['filename'];
        $extension = $info['extension'];

        // Lógica: quitar '_ppal' u otros sufijos y poner '_thumb'
        // Cortamos hasta el último guion bajo '_'
        if (strrpos($filename, '_') !== false) {
            $baseName = substr($filename, 0, strrpos($filename, '_'));
        } else {
            $baseName = $filename; // Por seguridad, si no tiene guiones
        }

        // Usamos el disco `public` (tenant-aware): el TenantManager ajusta su URL
        // por tenant (p. ej. enjuego -> /storage/tenants/granadaenjuego). No
        // hardcodear "/storage/" o se rompe en los tenants con subcarpeta.
        return Storage::disk('public')->url("{$dirname}/{$baseName}_portada.{$extension}");
    }
}