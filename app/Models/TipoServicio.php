<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoServicio extends Model
{
    use HasFactory;

    protected $table = 'tipos_servicios';

    protected $fillable = [
        'nombre',
        'descripcion',
        'icono',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function actividades()
    {
        return $this->hasMany(Actividad::class);
    }

    public function solicitudes()
    {
        return $this->hasMany(Solicitud::class, 'tipo_servicio_id');
    }
}
