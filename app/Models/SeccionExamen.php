<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SeccionExamen extends Model
{
    use HasFactory;

    protected $table = 'secciones_examen';

    protected $fillable = [
        'examen_id',
        'materia_id',
        'puntaje',
        'respuestas_correctas',
        'total_preguntas',
        'tiempo_gastado',
    ];

    protected function casts(): array
    {
        return [
            'puntaje' => 'decimal:2',
            'respuestas_correctas' => 'integer',
            'total_preguntas' => 'integer',
            'tiempo_gastado' => 'integer',
        ];
    }

    public function examen(): BelongsTo
    {
        return $this->belongsTo(Examen::class);
    }

    public function materia(): BelongsTo
    {
        return $this->belongsTo(Materia::class);
    }

    public function respuestasUsuario(): HasMany
    {
        return $this->hasMany(RespuestaUsuario::class);
    }

    public function preguntas()
    {
        return $this->belongsToMany(Pregunta::class, 'pregunta_seccion_examen', 'seccion_examen_id', 'pregunta_id');
    }
}
