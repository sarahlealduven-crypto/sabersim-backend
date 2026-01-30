<?php

use App\Enums\EstadoExamen;
use App\Enums\TipoExamen;
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
        Schema::create('examenes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('tipo_examen', array_column(TipoExamen::cases(), 'value'))->default(TipoExamen::Completo->value);
            $table->enum('estado', array_column(EstadoExamen::cases(), 'value'))->default(EstadoExamen::EnProgreso->value);
            $table->timestamp('fecha_inicio');
            $table->timestamp('fecha_completado')->nullable();
            $table->decimal('puntaje_total', 5, 2)->nullable();
            $table->integer('tiempo_gastado')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('estado');
            $table->index('fecha_completado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('examenes');
    }
};
