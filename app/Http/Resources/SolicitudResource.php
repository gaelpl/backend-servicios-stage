<?php

namespace App\Http\Resources;

use App\Models\Solicitud;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SolicitudResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $fechaCreacion = optional($this->created_at)->toIso8601ZuluString();
        $fechaAsignacion = $this->fechaAsignacion();

        $lineaTiempo = [
            [
                'estado' => Solicitud::ESTADO_RECIBIDA,
                'fecha_cambio' => $fechaCreacion,
                'motivo' => null,
            ],
        ];

        if ($fechaAsignacion !== null) {
            $lineaTiempo[] = [
                'estado' => Solicitud::ESTADO_ASIGNADA,
                'fecha_cambio' => $fechaAsignacion,
                'motivo' => null,
            ];
        }

        if ($this->estado === Solicitud::ESTADO_CANCELADA) {
            $lineaTiempo[] = [
                'estado' => Solicitud::ESTADO_CANCELADA,
                'fecha_cambio' => optional($this->updated_at)->toIso8601ZuluString(),
                'motivo' => $this->motivo_cancelacion,
            ];
        }

        return [
            'folio' => $this->folio,
            'cliente_id' => $this->cliente_id,
            'tipo_servicio_id' => $this->tipo_servicio_id,
            'tipo_servicio' => $this->whenLoaded('tipoServicio', fn () => $this->tipoServicio ? new TipoServicioResource($this->tipoServicio) : null),
            'tipo_servicio_nombre' => $this->tipoServicio?->nombre ?? 'Servicio sin tipo',
            'prestador_id' => $this->prestador_id,
            'prestador' => $this->whenLoaded('prestador', fn () => $this->prestador ? new PrestadorResource($this->prestador) : null),
            'estado' => $this->estado,
            'descripcion_problema' => $this->descripcion_problema,
            'direccion_linea_1' => $this->direccion_linea_1,
            'ciudad' => $this->ciudad,
            'estado_direccion' => $this->estado_direccion,
            'codigo_postal' => $this->codigo_postal,
            'referencias_direccion' => $this->referencias_direccion,
            'fecha_preferida' => optional($this->fecha_preferida)->toDateString(),
            'materiales_notas' => $this->materiales_notas,
            'imagen_adjunta_url' => $this->imagen_adjunta_url,
            'motivo_cancelacion' => $this->motivo_cancelacion,
            'costo_total' => (float) $this->costo_total,
            'puede_cancelar' => $this->estado === Solicitud::ESTADO_RECIBIDA,
            'tiene_calificacion' => $this->relationLoaded('calificacion') ? $this->calificacion !== null : null,
            'fecha_creacion' => $fechaCreacion,
            'fecha_asignacion' => $fechaAsignacion,
            'linea_tiempo' => $lineaTiempo,
            'calificacion' => $this->whenLoaded('calificacion', fn () => $this->calificacion ? new CalificacionResource($this->calificacion) : null),
            'created_at' => $fechaCreacion,
            'updated_at' => optional($this->updated_at)->toIso8601ZuluString(),
        ];
    }

    private function fechaAsignacion(): ?string
    {
        if ($this->prestador_id === null) {
            return null;
        }

        if (! in_array($this->estado, [Solicitud::ESTADO_ASIGNADA, Solicitud::ESTADO_EN_PROCESO, Solicitud::ESTADO_TERMINADA], true)) {
            return null;
        }

        return optional($this->updated_at)->toIso8601ZuluString();
    }
}
