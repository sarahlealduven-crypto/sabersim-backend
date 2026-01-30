<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RespuestaUsuario extends Model
{
    use HasFactory;

    protected $table = 'respuestas_usuario';

    protected $fillable = [
        'seccion_examen_id',
        'pregunta_id',
        'opcion_seleccionada_id',
        'es_correcta',
        'tiempo_gastado',
    ];

    protected function casts(): array
    {
        return [
            'es_correcta' => 'boolean',
            'tiempo_gastado' => 'integer',
        ];
    }

    public function seccionExamen(): BelongsTo
    {
        return $this->belongsTo(SeccionExamen::class);
    }

    public function pregunta(): BelongsTo
    {
        return $this->belongsTo(Pregunta::class);
    }

    public function opcionSeleccionada(): BelongsTo
    {
        return $this->belongsTo(OpcionRespuesta::class, 'opcion_seleccionada_id');
    }
}
