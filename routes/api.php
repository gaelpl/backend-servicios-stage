<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PrestadorController;
use App\Http\Controllers\Api\SolicitudController;

// Endpoints de Prestadores
Route::get('/prestadors', [PrestadorController::class, 'index']);
Route::get('/prestadors/{id}', [PrestadorController::class, 'show']);

// Endpoints de Solicitudes
Route::get('/solicituds', [SolicitudController::class, 'index']);
Route::post('/solicituds', [SolicitudController::class, 'store']);
Route::get('/solicituds/{id}', [SolicitudController::class, 'show']);
Route::put('/solicituds/{id}/asignar', [SolicitudController::class, 'asignarPrestador']);
Route::put('/solicituds/{id}/estado', [SolicitudController::class, 'cambiarEstado']);