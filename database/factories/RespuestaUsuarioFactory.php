<?php

namespace Database\Factories;

use App\Models\OpcionRespuesta;
use App\Models\Pregunta;
use App\Models\SeccionExamen;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RespuestaUsuario>
 */
class RespuestaUsuarioFactory extends Factory
{
    /**
     * Define el estado predeterminado del modelo.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'seccion_examen_id' => SeccionExamen::factory(),
            'pregunta_id' => Pregunta::factory(),
            'opcion_seleccionada_id' => OpcionRespuesta::factory(),
            'es_correcta' => fake()->boolean(),
            'tiempo_gastado' => fake()->numberBetween(10, 180),
        ];
    }
}
