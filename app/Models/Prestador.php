<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prestador extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'telefono',
        'especialidad',
        'estado',
        'calificacion',
    ];

    public function solicitudes()
    {
        return $this->hasMany(Solicitud::class);
    }
}