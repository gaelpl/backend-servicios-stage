<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CalificacionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'solicitud_id' => $this->solicitud_id,
            'folio' => $this->whenLoaded('solicitud', fn () => $this->solicitud->folio),
            'puntuacion' => (int) $this->puntuacion,
            'comentario' => $this->comentario,
            'fecha_calificacion' => optional($this->created_at)->toIso8601ZuluString(),
            'created_at' => optional($this->created_at)->toIso8601ZuluString(),
            'updated_at' => optional($this->updated_at)->toIso8601ZuluString(),
        ];
    }
}
