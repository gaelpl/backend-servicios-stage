<?php

use App\Http\Controllers\Api\ArchivoController;
use App\Http\Controllers\Api\ServicioTipoController;
use App\Http\Controllers\Api\SolicitudController;
use Illuminate\Support\Facades\Route;

Route::prefix('servicios')->group(function () {
    // Catálogo de tipos de servicio
    Route::get('/tipos', [ServicioTipoController::class, 'index']);
    Route::get('/tipos/{tipoId}/actividades', [ServicioTipoController::class, 'actividades']);

    // Solicitudes
    Route::get('/solicitudes', [SolicitudController::class, 'index']);
    Route::post('/solicitudes', [SolicitudController::class, 'store']);
    Route::get('/solicitudes/{folio}', [SolicitudController::class, 'show']);
    Route::post('/solicitudes/{folio}/cancelar', [SolicitudController::class, 'cancelar']);

    // Calificaciones
    Route::post('/solicitudes/{folio}/calificacion', [SolicitudController::class, 'storeCalificacion']);
    Route::get('/solicitudes/{folio}/calificacion', [SolicitudController::class, 'showCalificacion']);

    // Carga de archivos
    Route::post('/archivos', [ArchivoController::class, 'store']);
});
