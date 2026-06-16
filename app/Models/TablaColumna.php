<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TablaColumna extends Model
{
    protected $table = 'tablas_columnas';

    protected $fillable = [
        'tabla_id',
        'posicion',
        'nombre',
    ];

    public function tabla()
    {
        return $this->belongsTo(Tabla::class, 'tabla_id');
    }
}