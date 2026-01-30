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
        Schema::create('estadisticas_usuarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('materia_id')->nullable()->constrained()->onDelete('cascade');
            $table->integer('total_examenes')->default(0);
            $table->integer('total_preguntas_respondidas')->default(0);
            $table->integer('respuestas_correctas')->default(0);
            $table->decimal('puntaje_promedio', 5, 2)->default(0);
            $table->decimal('mejor_puntaje', 5, 2)->default(0);
            $table->integer('tiempo_total_gastado')->default(0);
            $table->timestamp('fecha_ultimo_examen')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->unique(['user_id', 'materia_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estadisticas_usuario');
    }
};
