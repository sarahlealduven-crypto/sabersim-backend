<?php

namespace Database\Factories;

use App\Models\Examen;
use App\Models\Materia;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SeccionExamen>
 */
class SeccionExamenFactory extends Factory
{
    /**
     * Define el estado predeterminado del modelo.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'examen_id' => Examen::factory(),
            'materia_id' => Materia::factory(),
            'puntaje' => fake()->randomFloat(0, 100),
            'respuestas_correctas' => fake()->numberBetween(0, 50),
            'total_preguntas' => fake()->numberBetween(30, 50),
            'tiempo_gastado' => fake()->numberBetween(300, 5400),
        ];
    }
}
