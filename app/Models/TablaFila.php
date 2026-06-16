<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TablaFila extends Model
{
    protected $table = 'tablas_filas';

    protected $fillable = [
        'tabla_id',
        'posicion',
    ];

    public function tabla()
    {
        return $this->belongsTo(Tabla::class, 'tabla_id');
    }

    public function celdas()
    {
        return $this->hasMany(TablaCelda::class, 'fila_id');
    }
}