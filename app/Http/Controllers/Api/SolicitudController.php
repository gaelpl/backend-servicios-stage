<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CalificacionResource;
use App\Http\Resources\SolicitudResource;
use App\Models\Solicitud;
use App\Support\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class SolicitudController extends Controller
{
    public function index(Request $request)
    {
        $estado = $request->query('estado');

        if ($estado !== null && ! in_array($estado, Solicitud::ESTADOS, true)) {
            return ApiResponse::error(
                'VALIDATION_ERROR',
                'El estado proporcionado no es válido.',
                ['estado' => ['Los valores permitidos son: '.implode(', ', Solicitud::ESTADOS).'.']],
                422,
            );
        }

        $perPage = (int) $request->query('per_page', 20);
        $perPage = max(1, min($perPage, 100));

        $query = Solicitud::query()
            ->with(['prestador', 'tipoServicio', 'calificacion'])
            ->latest('created_at');

        if ($estado !== null) {
            $query->where('estado', $estado);
        }

        $solicitudes = $query->paginate($perPage);

        return ApiResponse::success(
            SolicitudResource::collection($solicitudes->getCollection())->resolve(),
            200,
            ApiResponse::pagination($solicitudes),
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tipo_servicio_id' => 'required|integer|exists:tipos_servicios,id',
            'descripcion_problema' => 'required|string',
            'direccion_linea_1' => 'nullable|string|max:255',
            'ciudad' => 'nullable|string|max:255',
            'estado_direccion' => 'nullable|string|max:255',
            'codigo_postal' => 'nullable|string|max:10',
            'referencias_direccion' => 'nullable|string|max:255',
            'fecha_preferida' => 'nullable|date',
            'materiales_notas' => 'nullable|string',
            'imagen_adjunta_url' => 'nullable|string|max:2048',
            'costo_total' => 'nullable|numeric|min:0',
        ]);

        $solicitud = Solicitud::create([
            ...$validated,
            'folio' => $this->generarFolio(),
            'estado' => Solicitud::ESTADO_RECIBIDA,
            'cliente_id' => $request->input('cliente_id', 1),
            'direccion_linea_1' => $validated['direccion_linea_1'] ?? 'Sin dirección',
            'codigo_postal' => $validated['codigo_postal'] ?? '81200',
            'fecha_preferida' => isset($validated['fecha_preferida']) && $validated['fecha_preferida']
                ? Carbon::parse($validated['fecha_preferida'])->toDateString()
                : now()->toDateString(),
        ]);

        return ApiResponse::success(
            (new SolicitudResource($solicitud->load(['prestador', 'tipoServicio', 'calificacion'])))->resolve(),
            201,
        );
    }

    public function show(string $folio)
    {
        $solicitud = Solicitud::query()
            ->with(['prestador', 'tipoServicio', 'calificacion'])
            ->where('folio', $folio)
            ->firstOrFail();

        return ApiResponse::success((new SolicitudResource($solicitud))->resolve());
    }

    public function cancelar(Request $request, string $folio)
    {
        $solicitud = Solicitud::where('folio', $folio)->firstOrFail();

        if ($solicitud->estado === Solicitud::ESTADO_CANCELADA) {
            return ApiResponse::error('CONFLICT', 'La solicitud ya se encuentra cancelada.', [], 409);
        }

        $validated = $request->validate([
            'motivo' => 'required|string|max:1000',
        ]);

        $solicitud->estado = Solicitud::ESTADO_CANCELADA;
        $solicitud->motivo_cancelacion = $validated['motivo'];
        $solicitud->save();

        return ApiResponse::success(
            (new SolicitudResource($solicitud->load(['prestador', 'tipoServicio', 'calificacion'])))->resolve(),
        );
    }

    public function storeCalificacion(Request $request, string $folio)
    {
        $solicitud = Solicitud::where('folio', $folio)->firstOrFail();

        if ($solicitud->calificacion()->exists()) {
            return ApiResponse::error('CONFLICT', 'La solicitud ya cuenta con una calificación.', [], 409);
        }

        $validated = $request->validate([
            'puntuacion' => ['required', 'integer', 'between:1,5'],
            'comentario' => 'nullable|string|max:2000',
        ]);

        $calificacion = $solicitud->calificacion()->create($validated);

        return ApiResponse::success(
            (new CalificacionResource($calificacion->load('solicitud')))->resolve(),
            201,
        );
    }

    public function showCalificacion(string $folio)
    {
        $solicitud = Solicitud::where('folio', $folio)->firstOrFail();

        $calificacion = $solicitud->calificacion;

        if (! $calificacion) {
            return response()->noContent();
        }

        return ApiResponse::success((new CalificacionResource($calificacion->load('solicitud')))->resolve());
    }

    private function generarFolio(): string
    {
        $prefix = 'SOL-'.now()->year.'-';

        $ultimo = Solicitud::query()
            ->where('folio', 'like', $prefix.'%')
            ->orderByDesc('folio')
            ->value('folio');

        $consecutivo = $ultimo ? ((int) substr($ultimo, strlen($prefix))) + 1 : 1;

        return $prefix.str_pad((string) $consecutivo, 4, '0', STR_PAD_LEFT);
    }
}
