<?php

use App\Enums\NivelDificultad;
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
        Schema::create('preguntas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('materia_id')->constrained()->onDelete('cascade');
            $table->foreignId('topico_id')->nullable()->constrained()->onDelete('set null');
            $table->text('texto_pregunta');
            $table->text('texto_contexto')->nullable();
            $table->enum('nivel_dificultad', array_column(NivelDificultad::cases(), 'value'))->default(NivelDificultad::Medio->value);
            $table->text('explicacion')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index('materia_id');
            $table->index('nivel_dificultad');
            $table->index('activo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('preguntas');
    }
};
