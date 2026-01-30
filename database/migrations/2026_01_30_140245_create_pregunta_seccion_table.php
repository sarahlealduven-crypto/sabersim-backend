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
        Schema::create('pregunta_seccion_examen', function (Blueprint $table) {
            $table->foreignId('pregunta_id')->constrained('preguntas')->onDelete('cascade');
            $table->foreignId('seccion_examen_id')->constrained('secciones_examen')->onDelete('cascade');
            $table->timestamps();

            $table->index('pregunta_id');
            $table->index('seccion_examen_id');
            $table->unique(['pregunta_id', 'seccion_examen_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pregunta_seccion_examen');
    }
};
