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
        Schema::create('secciones_examen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('examen_id')->constrained('examenes')->onDelete('cascade');
            $table->foreignId('materia_id')->constrained('materias')->onDelete('cascade');
            $table->decimal('puntaje', 5, 2)->nullable();
            $table->integer('respuestas_correctas')->default(0);
            $table->integer('total_preguntas');
            $table->integer('tiempo_gastado')->nullable();
            $table->timestamps();

            $table->index('examen_id');
            $table->index('materia_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('secciones_examen');
    }
};
