<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
{
    Schema::create('solicituds', function (Blueprint $table) {
        $table->id();
        $table->string('folio')->unique();
        $table->unsignedBigInteger('cliente_id')->default(1);
        $table->unsignedBigInteger('tipo_servicio_id')->nullable();
        $table->foreignId('prestador_id')->nullable()->constrained('prestadors')->nullOnDelete();
        $table->string('estado')->default('recibida'); // recibida, asignada, en_proceso, terminada, cancelada, reclamo, reembolso
        $table->text('descripcion_problema');
        $table->string('direccion_linea_1');
        $table->string('ciudad')->nullable();
        $table->string('estado_direccion')->nullable();
        $table->string('codigo_postal')->nullable();
        $table->string('referencias_direccion')->nullable();
        $table->string('fecha_preferida');
        $table->text('materiales_notas')->nullable();
        $table->string('imagen_adjunta_url')->nullable();
        $table->text('motivo_cancelacion')->nullable();
        $table->decimal('costo_total', 10, 2)->default(0.00);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicituds');
    }
};
