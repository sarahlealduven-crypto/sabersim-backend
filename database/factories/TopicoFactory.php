<?php

namespace Database\Factories;

use App\Models\Materia;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Topico>
 */
class TopicoFactory extends Factory
{
    /**
     * Define el estado predeterminado del modelo.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $materiaId = Materia::inRandomOrder()->first()?->id ?? 1;

        $topicosPorMateria = [
            1 => [ // Lectura Crítica
                'Comprensión literal', 'Comprensión inferencial', 'Comprensión crítica',
                'Análisis de textos narrativos', 'Análisis de textos argumentativos', 'Análisis de textos informativos',
                'Estructuras gramaticales', 'Vocabulario contextual', 'Figuras literarias',
            ],
            2 => [ // Matemáticas
                'Álgebra lineal', 'Álgebra de funciones', 'Ecuaciones y desigualdades',
                'Geometría analítica', 'Geometría euclidiana', 'Trigonometría',
                'Estadística descriptiva', 'Probabilidad', 'Números y operaciones',
                'Funciones y modelación',
            ],
            3 => [ // Sociales y Ciudadanas
                'Historia de Colombia', 'Historia universal', 'Geografía física',
                'Geografía humana', 'Constitución política', 'Democracia y participación',
                'Economía', 'Cultura y sociedad', 'Relaciones internacionales',
                'Derechos humanos',
            ],
            4 => [ // Ciencias Naturales
                'Biología celular', 'Genética', 'Ecología', 'Evolución',
                'Química orgánica', 'Química inorgánica', 'Estequiometría',
                'Física mecánica', 'Física eléctrica', 'Termodinámica',
            ],
            5 => [ // Inglés
                'Reading comprehension', 'Grammar', 'Vocabulary',
                'Listening comprehension', 'Writing skills', 'Speaking skills',
                'Verb tenses', 'Prepositions', 'Phrasal verbs', 'Idioms',
            ],
        ];

        $nombre = fake()->randomElement($topicosPorMateria[1]); // Default to first materia if not set

        return [
            'materia_id' => $materiaId,
            'nombre' => $nombre,
            'slug' => fake()->slug(),
            'descripcion' => fake()->sentence(),
        ];
    }

    /**
     * Crea un tema para Lectura Crítica.
     */
    public function lecturaCritica(): static
    {
        return $this->state(fn (array $attributes): array => [
            'nombre' => fake()->randomElement([
                'Comprensión literal', 'Comprensión inferencial', 'Comprensión crítica',
                'Análisis de textos narrativos', 'Análisis de textos argumentativos', 'Análisis de textos informativos',
                'Estructuras gramaticales', 'Vocabulario contextual', 'Figuras literarias',
            ]),
        ]);
    }

    /**
     * Crea un tema para Matemáticas.
     */
    public function matematicas(): static
    {
        return $this->state(fn (array $attributes): array => [
            'nombre' => fake()->randomElement([
                'Álgebra lineal', 'Álgebra de funciones', 'Ecuaciones y desigualdades',
                'Geometría analítica', 'Geometría euclidiana', 'Trigonometría',
                'Estadística descriptiva', 'Probabilidad', 'Números y operaciones',
                'Funciones y modelación',
            ]),
        ]);
    }

    /**
     * Crea un tema para Sociales y Ciudadanas.
     */
    public function sociales(): static
    {
        return $this->state(fn (array $attributes): array => [
            'nombre' => fake()->randomElement([
                'Historia de Colombia', 'Historia universal', 'Geografía física',
                'Geografía humana', 'Constitución política', 'Democracia y participación',
                'Economía', 'Cultura y sociedad', 'Relaciones internacionales',
                'Derechos humanos',
            ]),
        ]);
    }

    /**
     * Crea un tema para Ciencias Naturales.
     */
    public function cienciasNaturales(): static
    {
        return $this->state(fn (array $attributes): array => [
            'nombre' => fake()->randomElement([
                'Biología celular', 'Genética', 'Ecología', 'Evolución',
                'Química orgánica', 'Química inorgánica', 'Estequiometría',
                'Física mecánica', 'Física eléctrica', 'Termodinámica',
            ]),
        ]);
    }

    /**
     * Crea un tema para Inglés.
     */
    public function ingles(): static
    {
        return $this->state(fn (array $attributes): array => [
            'nombre' => fake()->randomElement([
                'Reading comprehension', 'Grammar', 'Vocabulary',
                'Listening comprehension', 'Writing skills', 'Speaking skills',
                'Verb tenses', 'Prepositions', 'Phrasal verbs', 'Idioms',
            ]),
        ]);
    }
}
