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
        Schema::create('respuestas_usuario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seccion_examen_id')->constrained('secciones_examen')->onDelete('cascade');
            $table->foreignId('pregunta_id')->constrained('preguntas')->onDelete('cascade');
            $table->foreignId('opcion_seleccionada_id')->nullable()->constrained('opciones_respuesta')->onDelete('set null');
            $table->boolean('es_correcta')->nullable();
            $table->integer('tiempo_gastado')->nullable();
            $table->timestamps();

            $table->index('seccion_examen_id');
            $table->index('pregunta_id');
            $table->unique(['seccion_examen_id', 'pregunta_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('respuestas_usuario');
    }
};
