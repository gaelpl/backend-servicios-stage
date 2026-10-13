<?php

namespace Tests\Feature;

use App\Models\Actividad;
use App\Models\Solicitud;
use App\Models\TipoServicio;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ServiciosApiTest extends TestCase
{
    use RefreshDatabase;

    private function crearCatalogo(): TipoServicio
    {
        $tipo = TipoServicio::create([
            'nombre' => 'Plomería',
            'descripcion' => 'Fugas',
            'activo' => true,
        ]);

        Actividad::create([
            'tipo_servicio_id' => $tipo->id,
            'nombre' => 'Reparación de fuga',
            'costo_estimado' => 350,
            'activo' => true,
        ]);

        TipoServicio::create([
            'nombre' => 'Inactivo',
            'activo' => false,
        ]);

        return $tipo;
    }

    private function crearSolicitud(array $extra = []): Solicitud
    {
        $tipo = TipoServicio::firstOrCreate(['nombre' => 'Plomería'], ['activo' => true]);

        return Solicitud::create(array_merge([
            'folio' => 'SOL-2026-0001',
            'cliente_id' => 1,
            'tipo_servicio_id' => $tipo->id,
            'estado' => 'recibida',
            'descripcion_problema' => 'Fuga de agua',
            'direccion_linea_1' => 'Calle 1',
            'fecha_preferida' => '2026-10-15',
            'costo_total' => 350,
        ], $extra));
    }

    private function assertEnvelope(array $json): void
    {
        $this->assertArrayHasKey('data', $json);
        $this->assertArrayHasKey('meta', $json);
        $this->assertStringStartsWith('req-', $json['meta']['request_id']);
        $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}Z$/', $json['meta']['timestamp']);
    }

    public function test_listar_tipos_con_envoltura(): void
    {
        $this->crearCatalogo();

        $response = $this->getJson('/api/servicios/tipos');
        $response->assertOk();

        $this->assertEnvelope($response->json());
        $this->assertCount(2, $response->json('data'));
        $this->assertArrayNotHasKey('pagination', $response->json('meta'));
    }

    public function test_filtrar_tipos_activos(): void
    {
        $this->crearCatalogo();

        $response = $this->getJson('/api/servicios/tipos?activo=true');
        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
        $this->assertTrue($response->json('data.0.activo'));
    }

    public function test_listar_actividades_de_un_tipo(): void
    {
        $tipo = $this->crearCatalogo();

        $response = $this->getJson("/api/servicios/tipos/{$tipo->id}/actividades");
        $response->assertOk();
        $this->assertEnvelope($response->json());
        $this->assertSame('Reparación de fuga', $response->json('data.0.nombre'));
    }

    public function test_tipo_inexistente_retorna_error_envuelto(): void
    {
        $response = $this->getJson('/api/servicios/tipos/999/actividades');
        $response->assertStatus(404);
        $this->assertSame('NOT_FOUND', $response->json('error.code'));
        $this->assertArrayHasKey('meta', $response->json());
    }

    public function test_crear_solicitud_genera_folio(): void
    {
        $tipo = $this->crearCatalogo();

        $response = $this->postJson('/api/servicios/solicitudes', [
            'tipo_servicio_id' => $tipo->id,
            'descripcion_problema' => 'Fuga de agua',
            'direccion_linea_1' => 'Calle 1',
            'fecha_preferida' => '2026-10-15',
        ]);

        $response->assertStatus(201);
        $this->assertEnvelope($response->json());
        $this->assertMatchesRegularExpression('/^SOL-2026-\d{4}$/', $response->json('data.folio'));
        $this->assertSame('recibida', $response->json('data.estado'));
        $this->assertSame('2026-10-15', $response->json('data.fecha_preferida'));
    }

    public function test_validacion_retorna_error_estructurado(): void
    {
        $response = $this->postJson('/api/servicios/solicitudes', []);
        $response->assertStatus(422);
        $this->assertSame('VALIDATION_ERROR', $response->json('error.code'));
        $this->assertSame('Error de validación', $response->json('error.code_name'));
        $this->assertArrayHasKey('details', $response->json('error'));
    }

    public function test_listado_paginado_incluye_meta_pagination(): void
    {
        $this->crearSolicitud();

        $response = $this->getJson('/api/servicios/solicitudes?estado=recibida&page=1&per_page=10');
        $response->assertOk();
        $this->assertEnvelope($response->json());
        $this->assertSame(
            ['page' => 1, 'per_page' => 10, 'total' => 1, 'total_pages' => 1],
            $response->json('meta.pagination'),
        );
    }

    public function test_cancelar_solicitud(): void
    {
        $solicitud = $this->crearSolicitud();

        $response = $this->postJson("/api/servicios/solicitudes/{$solicitud->folio}/cancelar", [
            'motivo' => 'El cliente ya no requiere el servicio',
        ]);

        $response->assertOk();
        $this->assertSame('cancelada', $response->json('data.estado'));
        $this->assertSame('El cliente ya no requiere el servicio', $response->json('data.motivo_cancelacion'));
    }

    public function test_calificacion_ciclo_completo(): void
    {
        $solicitud = $this->crearSolicitud();

        $this->getJson("/api/servicios/solicitudes/{$solicitud->folio}/calificacion")
            ->assertStatus(204);

        $this->postJson("/api/servicios/solicitudes/{$solicitud->folio}/calificacion", [
            'puntuacion' => 5,
            'comentario' => 'Excelente servicio',
        ])->assertStatus(201)
            ->assertJsonPath('data.puntuacion', 5);

        $this->getJson("/api/servicios/solicitudes/{$solicitud->folio}/calificacion")
            ->assertOk()
            ->assertJsonPath('data.comentario', 'Excelente servicio');
    }

    public function test_crear_solicitud_con_solo_descripcion_aplica_defaults(): void
    {
        $response = $this->postJson('/api/servicios/solicitudes', [
            'descripcion' => 'Fuga de agua en tubería principal',
        ]);

        $response->assertStatus(201);
        $this->assertSame('Fuga de agua en tubería principal', $response->json('data.descripcion_problema'));
        $this->assertSame('Dirección registrada', $response->json('data.direccion_linea_1'));
        $this->assertSame('81200', $response->json('data.codigo_postal'));
        $this->assertSame(now()->toDateString(), $response->json('data.fecha_preferida'));
    }

    public function test_subir_archivo(): void
    {
        Storage::fake('public');

        $response = $this->post('/api/servicios/archivos', [
            'archivo' => UploadedFile::fake()->image('foto.jpg'),
        ]);

        $response->assertStatus(201);
        $this->assertStringStartsWith('req-', $response->json('meta.request_id'));
        $this->assertStringContainsString('/storage/servicios/archivos/', $response->json('data.url'));
        $this->assertStringStartsWith('id-', $response->json('data.referencia'));
    }
}
