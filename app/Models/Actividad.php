<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Actividad extends Model
{
    use HasFactory;

    protected $table = 'actividades';

    protected $fillable = [
        'tipo_servicio_id',
        'nombre',
        'descripcion',
        'costo_estimado',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'costo_estimado' => 'decimal:2',
    ];

    public function tipoServicio()
    {
        return $this->belongsTo(TipoServicio::class);
    }
}
