<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EstadisticaUsuario extends Model
{
    use HasFactory;

    protected $table = 'estadisticas_usuarios';

    protected $fillable = [
        'user_id',
        'materia_id',
        'total_examenes',
        'total_preguntas_respondidas',
        'respuestas_correctas',
        'puntaje_promedio',
        'mejor_puntaje',
        'tiempo_total_gastado',
        'fecha_ultimo_examen',
    ];

    protected function casts(): array
    {
        return [
            'total_examenes' => 'integer',
            'total_preguntas_respondidas' => 'integer',
            'respuestas_correctas' => 'integer',
            'puntaje_promedio' => 'decimal:2',
            'mejor_puntaje' => 'decimal:2',
            'tiempo_total_gastado' => 'integer',
            'fecha_ultimo_examen' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function materia(): BelongsTo
    {
        return $this->belongsTo(Materia::class);
    }
}
