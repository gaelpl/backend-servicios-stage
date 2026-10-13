<?php

namespace Database\Seeders;

use App\Models\Actividad;
use App\Models\Calificacion;
use App\Models\Solicitud;
use App\Models\TipoServicio;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->resetTablas();

        // 1. Tipos de servicio
        $plomeria = TipoServicio::create([
            'id' => 1,
            'nombre' => 'Plomería',
            'activo' => true,
        ]);

        $electricidad = TipoServicio::create([
            'id' => 2,
            'nombre' => 'Electricidad',
            'activo' => true,
        ]);

        // 2. Actividades por tipo
        Actividad::create([
            'tipo_servicio_id' => $plomeria->id,
            'nombre' => 'Fuga de agua',
            'activo' => true,
        ]);

        Actividad::create([
            'tipo_servicio_id' => $plomeria->id,
            'nombre' => 'Instalación de tubería',
            'activo' => true,
        ]);

        Actividad::create([
            'tipo_servicio_id' => $electricidad->id,
            'nombre' => 'Cortocircuito',
            'activo' => true,
        ]);

        Actividad::create([
            'tipo_servicio_id' => $electricidad->id,
            'nombre' => 'Instalación de contactos',
            'activo' => true,
        ]);

        // 3. Solicitudes de prueba
        Solicitud::create([
            'folio' => 'SV-20261012182606',
            'cliente_id' => 1,
            'tipo_servicio_id' => $plomeria->id,
            'prestador_id' => null,
            'estado' => 'recibida',
            'descripcion_problema' => 'Fuga de agua en tubería principal',
            'direccion_linea_1' => 'Sin dirección registrada',
            'fecha_preferida' => '2026-10-15',
            'costo_total' => 0.00,
        ]);

        Solicitud::create([
            'folio' => 'SV-20260910-0007',
            'cliente_id' => 1,
            'tipo_servicio_id' => $electricidad->id,
            'prestador_id' => null,
            'estado' => 'terminada',
            'descripcion_problema' => 'Revisión de cortocircuito',
            'direccion_linea_1' => 'Sin dirección registrada',
            'fecha_preferida' => '2026-09-10',
            'costo_total' => 0.00,
        ]);
    }

    private function resetTablas(): void
    {
        Calificacion::query()->delete();
        Solicitud::query()->delete();
        Actividad::query()->delete();
        TipoServicio::query()->delete();
    }
}
