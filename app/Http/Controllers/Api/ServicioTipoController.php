<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ActividadResource;
use App\Http\Resources\TipoServicioResource;
use App\Models\TipoServicio;
use App\Support\ApiResponse;
use Illuminate\Http\Request;

class ServicioTipoController extends Controller
{
    public function index(Request $request)
    {
        $query = TipoServicio::query()->withCount('actividades');

        if ($request->has('activo')) {
            $query->where('activo', filter_var($request->query('activo'), FILTER_VALIDATE_BOOLEAN));
        }

        return ApiResponse::success(
            TipoServicioResource::collection($query->orderBy('nombre')->get())->resolve(),
        );
    }

    public function actividades(Request $request, int $tipoId)
    {
        $tipo = TipoServicio::findOrFail($tipoId);

        $query = $tipo->actividades()->getQuery();

        $query->where(
            'activo',
            $request->has('activo') ? filter_var($request->query('activo'), FILTER_VALIDATE_BOOLEAN) : true,
        );

        return ApiResponse::success(
            ActividadResource::collection($query->orderBy('nombre')->get())->resolve(),
        );
    }
}
