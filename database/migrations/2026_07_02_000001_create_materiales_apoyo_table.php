<?php

use App\Models\Materia;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materiales_apoyo', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Materia::class)->nullable()->constrained()->nullOnDelete();
            $table->string('titulo', 160);
            $table->string('slug', 180)->unique();
            $table->text('descripcion')->nullable();
            $table->string('tipo', 30);
            $table->string('source_url', 2048);
            $table->string('embed_url', 2048);
            $table->string('thumbnail_url', 2048)->nullable();
            $table->string('duracion', 50)->nullable();
            $table->integer('orden_visualizacion')->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index(['activo', 'tipo']);
            $table->index(['materia_id', 'activo']);
            $table->index('orden_visualizacion');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materiales_apoyo');
    }
};
