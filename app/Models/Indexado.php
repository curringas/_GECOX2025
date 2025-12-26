<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Indexado extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'P0114_indexado';

    /**
     * The primary key associated with the table.
     * We assume 'Nombre' is the key since there is no 'id'.
     *
     * @var string
     */
    protected $primaryKey = 'Nombre';

    /**
     * Indicates if the model's ID is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The data type of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'Nombre',
        'Descripcion',
        'Keywords',
        'Facebook',
        'Twitter',
        'Google',
        'Youtube',
        'Instagram',
        'ContadorFacebook',
        'ContadorTwitter',
        'ContadorInstagram',
        'ContadorTelegram',
    ];
}
