<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Prestador;
use App\Models\Solicitud;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Crear prestadores de prueba
        $p1 = Prestador::create([
            'nombre' => 'Carlos Ramírez',
            'telefono' => '6681234567',
            'especialidad' => 'Electricidad y Plomería',
            'estado' => 'disponible',
            'calificacion' => 4.85,
        ]);

        $p2 = Prestador::create([
            'nombre' => 'Roberto Gómez',
            'telefono' => '6689876543',
            'especialidad' => 'Aire Acondicionado',
            'estado' => 'disponible',
            'calificacion' => 5.00,
        ]);

        // Crear solicitudes de prueba
        Solicitud::create([
            'folio' => 'SOL-2026-001',
            'cliente_id' => 1,
            'tipo_servicio_id' => 1,
            'prestador_id' => $p1->id,
            'estado' => 'asignada',
            'descripcion_problema' => 'Fuga de agua en tubería principal del patio.',
            'direccion_linea_1' => 'Av. Independencia #450',
            'ciudad' => 'Los Mochis',
            'estado_direccion' => 'Sinaloa',
            'codigo_postal' => '81200',
            'referencias_direccion' => 'Frente a la plaza principal',
            'fecha_preferida' => '2026-10-15 10:00 AM',
            'costo_total' => 350.00,
        ]);

        Solicitud::create([
            'folio' => 'SOL-2026-002',
            'cliente_id' => 1,
            'tipo_servicio_id' => 2,
            'prestador_id' => null,
            'estado' => 'recibida',
            'descripcion_problema' => 'Mantenimiento preventivo de minisplit 1.5 ton.',
            'direccion_linea_1' => 'Calle Leyva #123',
            'ciudad' => 'Los Mochis',
            'estado_direccion' => 'Sinaloa',
            'codigo_postal' => '81240',
            'referencias_direccion' => 'Portón blanco',
            'fecha_preferida' => '2026-10-16 04:00 PM',
            'costo_total' => 600.00,
        ]);
    }
}