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
    Schema::create('prestadors', function (Blueprint $table) {
        $table->id();
        $table->string('nombre');
        $table->string('telefono');
        $table->string('especialidad')->default('General');
        $table->string('estado')->default('disponible'); // disponible, ocupado, inactivo
        $table->decimal('calificacion', 3, 2)->default(5.00);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prestadors');
    }
};
