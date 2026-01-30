<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Materia extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'slug',
        'descripcion',
        'icono',
        'cantidad_preguntas',
        'tiempo_limite',
        'orden_visualizacion',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
            'cantidad_preguntas' => 'integer',
            'tiempo_limite' => 'integer',
            'orden_visualizacion' => 'integer',
        ];
    }

    public function topicos(): HasMany
    {
        return $this->hasMany(Topico::class);
    }

    public function preguntas(): HasMany
    {
        return $this->hasMany(Pregunta::class);
    }

    public function seccionesExamen(): HasMany
    {
        return $this->hasMany(SeccionExamen::class);
    }

    public function estadisticasUsuario(): HasMany
    {
        return $this->hasMany(EstadisticaUsuario::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
