<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArchivoController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'archivo' => 'required|file|max:10240',
        ]);

        $file = $validated['archivo'];
        $path = $file->store('servicios/archivos', 'public');

        $url = Storage::disk('public')->url($path);

        return ApiResponse::success([
            'url' => $url,
            'referencia' => 'id-'.Str::lower(Str::random(8)),
            'nombre' => $file->getClientOriginalName(),
            'path' => $path,
        ], 201);
    }
}
