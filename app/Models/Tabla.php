<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tabla extends Model
{
    protected $table = 'tablas';

    protected $fillable = [
        'nombre',
        'answer_id', 
        'data'
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function filas()
    {
        return $this->hasMany(TablaFila::class, 'tabla_id');
    }

    public function columnas()
    {
        return $this->hasMany(TablaColumna::class, 'tabla_id');
    }
}