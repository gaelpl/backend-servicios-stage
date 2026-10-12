<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Solicitud;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SolicitudController extends Controller
{
    // Listar solicitudes con su prestador asignado
    public function index()
    {
        return response()->json(Solicitud::with('prestador')->get(), 200);
    }

    // Crear una nueva solicitud
    public function store(Request $request)
    {
        $validated = $request->validate([
            'descripcion_problema' => 'required|string',
            'direccion_linea_1' => 'required|string',
            'fecha_preferida' => 'required|string',
            'ciudad' => 'nullable|string',
            'estado_direccion' => 'nullable|string',
            'codigo_postal' => 'nullable|string',
            'referencias_direccion' => 'nullable|string',
            'materiales_notas' => 'nullable|string',
            'costo_total' => 'nullable|numeric',
        ]);

        $validated['folio'] = 'SOL-2026-' . strtoupper(Str::random(5));
        $validated['estado'] = 'recibida';
        $validated['cliente_id'] = 1;

        $solicitud = Solicitud::create($validated);

        return response()->json($solicitud, 201);
    }

    // Consultar detalle de una solicitud
    public function show($id)
    {
        $solicitud = Solicitud::with('prestador')->find($id);
        if (!$solicitud) {
            return response()->json(['message' => 'Solicitud no encontrada'], 404);
        }
        return response()->json($solicitud, 200);
    }

    // Asignar prestador
    public function asignarPrestador(Request $request, $id)
    {
        $solicitud = Solicitud::find($id);
        if (!$solicitud) {
            return response()->json(['message' => 'Solicitud no encontrada'], 404);
        }

        $validated = $request->validate([
            'prestador_id' => 'required|exists:prestadors,id',
        ]);

        $solicitud->prestador_id = $validated['prestador_id'];
        $solicitud->estado = 'asignada';
        $solicitud->save();

        return response()->json($solicitud->load('prestador'), 200);
    }

    // Cambiar estado de solicitud
    public function cambiarEstado(Request $request, $id)
    {
        $solicitud = Solicitud::find($id);
        if (!$solicitud) {
            return response()->json(['message' => 'Solicitud no encontrada'], 404);
        }

        $validated = $request->validate([
            'estado' => 'required|string',
        ]);

        $solicitud->estado = $validated['estado'];
        $solicitud->save();

        return response()->json($solicitud, 200);
    }
}