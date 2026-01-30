<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OpcionRespuesta extends Model
{
    use HasFactory;

    protected $table = 'opciones_respuesta';

    protected $fillable = [
        'pregunta_id',
        'letra_opcion',
        'texto_opcion',
        'es_correcta',
    ];

    protected function casts(): array
    {
        return [
            'es_correcta' => 'boolean',
        ];
    }

    public function pregunta(): BelongsTo
    {
        return $this->belongsTo(Pregunta::class);
    }

    public function respuestasUsuario(): HasMany
    {
        return $this->hasMany(RespuestaUsuario::class, 'opcion_seleccionada_id');
    }
}
