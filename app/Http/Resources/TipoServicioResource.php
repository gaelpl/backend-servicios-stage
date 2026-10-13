<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TipoServicioResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'icono' => $this->icono,
            'activo' => (bool) $this->activo,
            'actividades_count' => $this->whenCounted('actividades'),
            'created_at' => optional($this->created_at)->toIso8601ZuluString(),
            'updated_at' => optional($this->updated_at)->toIso8601ZuluString(),
        ];
    }
}
