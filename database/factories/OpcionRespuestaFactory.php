<?php

namespace Database\Factories;

use App\Models\Pregunta;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OpcionRespuesta>
 */
class OpcionRespuestaFactory extends Factory
{
    /**
     * Define el estado predeterminado del modelo.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'pregunta_id' => Pregunta::factory(),
            'letra_opcion' => fake()->randomElement(['A', 'B', 'C', 'D']),
            'texto_opcion' => fake()->sentence(),
            'es_correcta' => fake()->boolean(25), // 25% chance of being correct
        ];
    }

    /**
     * Crea una opción de respuesta correcta.
     */
    public function correcta(): static
    {
        return $this->state(fn (array $attributes): array => [
            'es_correcta' => true,
        ]);
    }

    /**
     * Crea una opción de respuesta incorrecta.
     */
    public function incorrecta(): static
    {
        return $this->state(fn (array $attributes): array => [
            'es_correcta' => false,
        ]);
    }
}
