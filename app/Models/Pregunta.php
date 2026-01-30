<?php

namespace App\Models;

use App\Enums\NivelDificultad;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pregunta extends Model
{
    use HasFactory;

    protected $fillable = [
        'materia_id',
        'topico_id',
        'texto_pregunta',
        'texto_contexto',
        'nivel_dificultad',
        'explicacion',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'nivel_dificultad' => NivelDificultad::class,
            'activo' => 'boolean',
        ];
    }

    public function materia(): BelongsTo
    {
        return $this->belongsTo(Materia::class);
    }

    public function topico(): BelongsTo
    {
        return $this->belongsTo(Topico::class);
    }

    public function opcionesRespuesta(): HasMany
    {
        return $this->hasMany(OpcionRespuesta::class);
    }

    public function respuestasUsuario(): HasMany
    {
        return $this->hasMany(RespuestaUsuario::class);
    }

    public function seccionesExamen()
    {
        return $this->belongsToMany(SeccionExamen::class, 'pregunta_seccion_examen', 'pregunta_id', 'seccion_examen_id');
    }

    public function opcionCorrecta(): HasOne
    {
        return $this->hasOne(OpcionRespuesta::class)->where('es_correcta', true);
    }
}
