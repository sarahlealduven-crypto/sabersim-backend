<?php

namespace Database\Factories;

use App\Models\Materia;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EstadisticaUsuario>
 */
class EstadisticaUsuarioFactory extends Factory
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
            'materia_id' => Materia::factory(),
            'total_examenes' => fake()->numberBetween(1, 50),
            'total_preguntas_respondidas' => fake()->numberBetween(50, 500),
            'respuestas_correctas' => fake()->numberBetween(20, 400),
            'puntaje_promedio' => fake()->randomFloat(0, 100),
            'mejor_puntaje' => fake()->randomFloat(0, 100),
            'tiempo_total_gastado' => fake()->numberBetween(3600, 100000),
            'fecha_ultimo_examen' => fake()->dateTime(),
        ];
    }
}
