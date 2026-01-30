<?php

namespace Database\Factories;

use App\Enums\EstadoExamen;
use App\Enums\TipoExamen;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Examen>
 */
class ExamenFactory extends Factory
{
    /**
     * Define el estado predeterminado del modelo.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'tipo_examen' => fake()->randomElement(TipoExamen::cases()),
            'estado' => EstadoExamen::EnProgreso,
            'fecha_inicio' => fake()->dateTime(),
            'fecha_completado' => null,
            'puntaje_total' => null,
            'tiempo_gastado' => null,
        ];
    }
}
