<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Materia>
 */
class MateriaFactory extends Factory
{
    /**
     * Define el estado predeterminado del modelo.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $baseMaterias = [
            [
                'nombre' => 'Lectura Crítica',
                'slug' => 'lectura-critica',
                'descripcion' => 'Comprensión lectora, análisis de textos y capacidad de interpretar diferentes tipos de información.',
                'icono' => 'book-open',
                'cantidad_preguntas' => 41,
                'tiempo_limite' => 60,
                'orden_visualizacion' => 1,
            ],
            [
                'nombre' => 'Matemáticas',
                'slug' => 'matematicas',
                'descripcion' => 'Álgebra, geometría, estadística y resolución de problemas matemáticos.',
                'icono' => 'calculator',
                'cantidad_preguntas' => 50,
                'tiempo_limite' => 80,
                'orden_visualizacion' => 2,
            ],
            [
                'nombre' => 'Sociales y Ciudadanas',
                'slug' => 'sociales',
                'descripcion' => 'Historia, geografía, constitución y cívica.',
                'icono' => 'globe',
                'cantidad_preguntas' => 50,
                'tiempo_limite' => 70,
                'orden_visualizacion' => 3,
            ],
            [
                'nombre' => 'Ciencias Naturales',
                'slug' => 'ciencias-naturales',
                'descripcion' => 'Biología, química y física.',
                'icono' => 'flask',
                'cantidad_preguntas' => 50,
                'tiempo_limite' => 60,
                'orden_visualizacion' => 4,
            ],
            [
                'nombre' => 'Inglés',
                'slug' => 'ingles',
                'descripcion' => 'Comprensión de lectura, gramática y vocabulario en inglés.',
                'icono' => 'language',
                'cantidad_preguntas' => 50,
                'tiempo_limite' => 50,
                'orden_visualizacion' => 5,
            ],
        ];

        $materia = fake()->randomElement($baseMaterias);

        return [
            'nombre' => $materia['nombre'],
            'slug' => $materia['slug'].'-'.fake()->unique()->randomNumber(5),
            'descripcion' => $materia['descripcion'],
            'icono' => $materia['icono'],
            'cantidad_preguntas' => $materia['cantidad_preguntas'],
            'tiempo_limite' => $materia['tiempo_limite'],
            'orden_visualizacion' => $materia['orden_visualizacion'],
            'activo' => true,
        ];
    }

    /**
     * Crea una materia de Lectura Crítica.
     */
    public function lecturaCritica(): static
    {
        return $this->state(fn (array $attributes): array => [
            'nombre' => 'Lectura Crítica',
            'slug' => 'lectura-critica-'.fake()->unique()->randomNumber(5),
            'descripcion' => 'Comprensión lectora, análisis de textos y capacidad de interpretar diferentes tipos de información.',
            'icono' => 'book-open',
            'cantidad_preguntas' => 41,
            'tiempo_limite' => 60,
            'orden_visualizacion' => 1,
        ]);
    }

    /**
     * Crea una materia de Matemáticas.
     */
    public function matematicas(): static
    {
        return $this->state(fn (array $attributes): array => [
            'nombre' => 'Matemáticas',
            'slug' => 'matematicas-'.fake()->unique()->randomNumber(5),
            'descripcion' => 'Álgebra, geometría, estadística y resolución de problemas matemáticos.',
            'icono' => 'calculator',
            'cantidad_preguntas' => 50,
            'tiempo_limite' => 80,
            'orden_visualizacion' => 2,
        ]);
    }

    /**
     * Crea una materia de Sociales y Ciudadanas.
     */
    public function sociales(): static
    {
        return $this->state(fn (array $attributes): array => [
            'nombre' => 'Sociales y Ciudadanas',
            'slug' => 'sociales-'.fake()->unique()->randomNumber(5),
            'descripcion' => 'Historia, geografía, constitución y cívica.',
            'icono' => 'globe',
            'cantidad_preguntas' => 50,
            'tiempo_limite' => 70,
            'orden_visualizacion' => 3,
        ]);
    }

    /**
     * Crea una materia de Ciencias Naturales.
     */
    public function cienciasNaturales(): static
    {
        return $this->state(fn (array $attributes): array => [
            'nombre' => 'Ciencias Naturales',
            'slug' => 'ciencias-naturales-'.fake()->unique()->randomNumber(5),
            'descripcion' => 'Biología, química y física.',
            'icono' => 'flask',
            'cantidad_preguntas' => 50,
            'tiempo_limite' => 60,
            'orden_visualizacion' => 4,
        ]);
    }

    /**
     * Crea una materia de Inglés.
     */
    public function ingles(): static
    {
        return $this->state(fn (array $attributes): array => [
            'nombre' => 'Inglés',
            'slug' => 'ingles-'.fake()->unique()->randomNumber(5),
            'descripcion' => 'Comprensión de lectura, gramática y vocabulario en inglés.',
            'icono' => 'language',
            'cantidad_preguntas' => 50,
            'tiempo_limite' => 50,
            'orden_visualizacion' => 5,
        ]);
    }

    /**
     * Marca la materia como inactiva.
     */
    public function inactiva(): static
    {
        return $this->state(fn (array $attributes): array => [
            'activo' => false,
        ]);
    }
}
