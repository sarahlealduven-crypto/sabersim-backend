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

    /**
     * @var list<string>
     */
    public const LETRAS_DISPONIBLES = ['A', 'B', 'C', 'D', 'E', 'F'];

    protected function casts(): array
    {
        return [
            'es_correcta' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (OpcionRespuesta $opcion): void {
            if (blank($opcion->letra_opcion)) {
                $opcion->letra_opcion = $opcion->siguienteLetraDisponible();
            }
        });
    }

    protected function siguienteLetraDisponible(): string
    {
        $letrasUsadas = static::query()
            ->where('pregunta_id', $this->pregunta_id)
            ->pluck('letra_opcion')
            ->all();

        $letrasDisponibles = self::LETRAS_DISPONIBLES;

        foreach ($letrasDisponibles as $letra) {
            if (! in_array($letra, $letrasUsadas, true)) {
                return $letra;
            }
        }

        return chr(ord((string) end($letrasDisponibles)) + 1);
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
