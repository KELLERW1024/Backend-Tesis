<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TablaCelda extends Model
{
    protected $table = 'tablas_celdas';

    protected $fillable = [
        'fila_id',
        'columna_id',
        'valor',
    ];

    public function fila()
    {
        return $this->belongsTo(TablaFila::class, 'fila_id');
    }
}