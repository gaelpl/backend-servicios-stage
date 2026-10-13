<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActividadResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tipo_servicio_id' => $this->tipo_servicio_id,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'costo_estimado' => $this->costo_estimado !== null ? (float) $this->costo_estimado : null,
            'precio_referencia' => $this->costo_estimado !== null ? (float) $this->costo_estimado : null,
            'activo' => (bool) $this->activo,
            'created_at' => optional($this->created_at)->toIso8601ZuluString(),
            'updated_at' => optional($this->updated_at)->toIso8601ZuluString(),
        ];
    }
}
