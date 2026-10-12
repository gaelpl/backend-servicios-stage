<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Prestador;
use Illuminate\Http\Request;

class PrestadorController extends Controller
{
    // Listar padrón de prestadores
    public function index()
    {
        return response()->json(Prestador::all(), 200);
    }

    // Consultar detalle de un prestador
    public function show($id)
    {
        $prestador = Prestador::find($id);
        if (!$prestador) {
            return response()->json(['message' => 'Prestador no encontrado'], 404);
        }
        return response()->json($prestador, 200);
    }
}