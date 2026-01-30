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
        Schema::create('opciones_respuesta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pregunta_id')->constrained()->onDelete('cascade');
            $table->char('letra_opcion', 1);
            $table->text('texto_opcion');
            $table->boolean('es_correcta')->default(false);
            $table->timestamps();

            $table->index('pregunta_id');
            $table->unique(['pregunta_id', 'letra_opcion']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opciones_respuesta');
    }
};
