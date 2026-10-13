<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Solicitud extends Model
{
    use HasFactory;

    public const ESTADO_RECIBIDA = 'recibida';

    public const ESTADO_ASIGNADA = 'asignada';

    public const ESTADO_EN_PROCESO = 'en_proceso';

    public const ESTADO_TERMINADA = 'terminada';

    public const ESTADO_CANCELADA = 'cancelada';

    public const ESTADO_RECLAMO = 'reclamo';

    public const ESTADO_REEMBOLSO = 'reembolso';

    public const ESTADOS = [
        self::ESTADO_RECIBIDA,
        self::ESTADO_ASIGNADA,
        self::ESTADO_EN_PROCESO,
        self::ESTADO_TERMINADA,
        self::ESTADO_CANCELADA,
        self::ESTADO_RECLAMO,
        self::ESTADO_REEMBOLSO,
    ];

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

    protected $casts = [
        'fecha_preferida' => 'date',
        'costo_total' => 'decimal:2',
    ];

    public function prestador()
    {
        return $this->belongsTo(Prestador::class);
    }

    public function tipoServicio()
    {
        return $this->belongsTo(TipoServicio::class, 'tipo_servicio_id');
    }

    public function calificacion()
    {
        return $this->hasOne(Calificacion::class);
    }
}
