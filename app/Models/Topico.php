<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Topico extends Model
{
    use HasFactory;

    protected $fillable = [
        'materia_id',
        'nombre',
        'slug',
        'descripcion',
    ];

    public function materia(): BelongsTo
    {
        return $this->belongsTo(Materia::class);
    }

    public function preguntas(): HasMany
    {
        return $this->hasMany(Pregunta::class);
    }
}
