<?php
// ...existing code...
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Banner extends Model
{
    protected $table = 'P0114_banner';
    protected $primaryKey = 'Identificador';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'Banner',
        'Titulo',
        'URL',
        'Tipo',
        'Creador',
        'Fecha',
        'Target',
        'Codigo',
    ];

    protected $guarded = [
        'Identificador',
        'Creador',
    ];

    // relación con la tabla de relación
    public function categorias()
    {
        return $this->belongsToMany(
            Categoria::class,           // Modelo relacionado
            'P0114_bannerenpagina',      // Nombre de la tabla pivote
            'Banner',            // Clave foránea de este modelo
            'Pagina'              // Clave foránea del otro modelo
        )->withPivot('Posicion','Orden');
    }

    public static function getNextOrder(int $paginaId, int $posicion): int
    {
        // 1. Especifica el nombre de tu tabla pivot. 
        $pivotTableName = 'P0114_bannerenpagina'; 
        
        // 2. Ejecutar la consulta para encontrar el máximo 'Orden'
        $maxOrder = DB::table($pivotTableName)
            ->where('Pagina', $paginaId) // Columna ID de la página/categoría en la tabla pivot
            ->where('Posicion', $posicion) // Columna Posicion en la tabla pivot
            ->max('Orden');

        // 3. Si no hay registros, el máximo es null o 0. Empezamos en 1.
        // Si hay un máximo, le sumamos 1.
        return ($maxOrder === null) ? 0 : $maxOrder + 1;
    }
}