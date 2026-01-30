<?php

namespace App\Models;

use App\Enums\EstadoExamen;
use App\Enums\TipoExamen;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Examen extends Model
{
    use HasFactory;

    protected $table = 'examenes';

    protected $fillable = [
        'user_id',
        'tipo_examen',
        'estado',
        'fecha_inicio',
        'fecha_completado',
        'puntaje_total',
        'tiempo_gastado',
    ];

    protected function casts(): array
    {
        return [
            'tipo_examen' => TipoExamen::class,
            'estado' => EstadoExamen::class,
            'fecha_inicio' => 'datetime',
            'fecha_completado' => 'datetime',
            'puntaje_total' => 'decimal:2',
            'tiempo_gastado' => 'integer',
        ];
    }

    public function isCompleted(): bool
    {
        return $this->estado === EstadoExamen::Completado;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function seccionesExamen(): HasMany
    {
        return $this->hasMany(SeccionExamen::class);
    }
}
