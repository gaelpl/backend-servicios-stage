<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Solicitud extends Model
{
    use HasFactory;

    protected $fillable = [
        'folio',
        'cliente_id',
        'tipo_servicio_id',
        'prestador_id',
        'estado',
        'descripcion_problema',
        'direccion_linea_1',
        'ciudad',
        'estado_direccion',
        'codigo_postal',
        'referencias_direccion',
        'fecha_preferida',
        'materiales_notas',
        'imagen_adjunta_url',
        'motivo_cancelacion',
        'costo_total',
    ];

    public function prestador()
    {
        return $this->belongsTo(Prestador::class);
    }
}