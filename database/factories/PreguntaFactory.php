<?php

namespace Database\Factories;

use App\Enums\NivelDificultad;
use App\Models\Materia;
use App\Models\Topico;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pregunta>
 */
class PreguntaFactory extends Factory
{
    /**
     * Define el estado predeterminado del modelo.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $materiaId = Materia::inRandomOrder()->first()?->id ?? 1;
        $dificultad = fake()->randomElement(NivelDificultad::cases());

        $templatesPorMateria = [
            1 => [ // Lectura Crítica
                [
                    'texto_pregunta' => 'De acuerdo con el texto anterior, ¿cuál es la idea principal que el autor expone?',
                    'texto_contexto' => 'El texto anterior presenta un análisis detallado sobre las relaciones sociales en las comunidades rurales colombianas, destacando la importancia de las tradiciones y el papel de la familia en la preservación de la identidad cultural.',
                    'explicacion' => 'La pregunta evalúa la capacidad de identificar la idea central del texto.',
                ],
                [
                    'texto_pregunta' => '¿Qué función cumple la expresión subrayada en el contexto del texto?',
                    'texto_contexto' => 'La propuesta educativa busca transformar la realidad de los estudiantes mediante estrategias innovadoras que fomenten el pensamiento crítico.',
                    'explicacion' => 'La pregunta analiza el uso contextual de expresiones lingüísticas.',
                ],
                [
                    'texto_pregunta' => '¿Cuál de los siguientes enunciados resume mejor el contenido del fragmento?',
                    'texto_contexto' => 'La biodiversidad en Colombia es una de las más ricas del mundo, con ecosistemas que van desde selvas tropicales hasta páramos andinos.',
                    'explicacion' => 'La pregunta evalúa la capacidad de síntesis y comprensión global.',
                ],
                [
                    'texto_pregunta' => 'Según el autor, ¿qué consecuencia directa trae la situación descrita?',
                    'texto_contexto' => 'El cambio climático ha provocado alteraciones significativas en los ciclos de lluvia en la región, afectando directamente la producción agrícola tradicional.',
                    'explicacion' => 'La pregunta analiza relaciones de causa y efecto.',
                ],
            ],
            2 => [ // Matemáticas
                [
                    'texto_pregunta' => 'Si f(x) = 2x² - 3x + 5, ¿cuál es el valor de f(4)?',
                    'texto_contexto' => null,
                    'explicacion' => 'Sustitución de x por 4 en la función cuadrática.',
                ],
                [
                    'texto_pregunta' => '¿Cuál es la solución de la ecuación 3x + 7 = 22?',
                    'texto_contexto' => null,
                    'explicacion' => 'Despeje de variable en ecuación lineal de primer grado.',
                ],
                [
                    'texto_pregunta' => 'En un triángulo rectángulo, si un cateto mide 3 cm y el otro 4 cm, ¿cuál es la longitud de la hipotenusa?',
                    'texto_contexto' => null,
                    'explicacion' => 'Aplicación del teorema de Pitágoras.',
                ],
                [
                    'texto_pregunta' => '¿Cuál es la probabilidad de obtener un número par al lanzar un dado estándar de 6 caras?',
                    'texto_contexto' => null,
                    'explicacion' => 'Cálculo de probabilidad en experimentos aleatorios.',
                ],
            ],
            3 => [ // Sociales y Ciudadanas
                [
                    'texto_pregunta' => '¿Cuál fue el principal objetivo de la independencia de Colombia?',
                    'texto_contexto' => null,
                    'explicacion' => 'Comprensión de los procesos históricos de independencia.',
                ],
                [
                    'texto_pregunta' => '¿Qué departamento colombiano se encuentra en la región Caribe?',
                    'texto_contexto' => null,
                    'explicacion' => 'Conocimiento de la geografía política de Colombia.',
                ],
                [
                    'texto_pregunta' => '¿Cuál de las siguientes es una garantía constitucional en Colombia?',
                    'texto_contexto' => null,
                    'explicacion' => 'Comprensión de los derechos y garantías constitucionales.',
                ],
                [
                    'texto_pregunta' => '¿Qué derecho fundamental protege la libertad de expresión?',
                    'texto_contexto' => null,
                    'explicacion' => 'Identificación de derechos fundamentales.',
                ],
            ],
            4 => [ // Ciencias Naturales
                [
                    'texto_pregunta' => '¿Qué orgánulo celular es responsable de la producción de energía?',
                    'texto_contexto' => null,
                    'explicacion' => 'Conocimiento de la estructura y función celular.',
                ],
                [
                    'texto_pregunta' => '¿Cuál es la fórmula química del agua?',
                    'texto_contexto' => null,
                    'explicacion' => 'Conocimiento básico de compuestos químicos.',
                ],
                [
                    'texto_pregunta' => '¿Qué ley física establece la relación entre fuerza, masa y aceleración?',
                    'texto_contexto' => null,
                    'explicacion' => 'Segunda ley de Newton.',
                ],
                [
                    'texto_pregunta' => '¿Qué proceso es fundamental para la fotosíntesis?',
                    'texto_contexto' => null,
                    'explicacion' => 'Comprensión de los procesos metabólicos de las plantas.',
                ],
            ],
            5 => [ // Inglés
                [
                    'texto_pregunta' => 'Read following text and answer: What is main purpose of the text?',
                    'texto_contexto' => 'The new environmental policy aims to reduce carbon emissions by 50% over the next decade through renewable energy adoption and sustainable practices.',
                    'explicacion' => 'Reading comprehension: identifying main idea.',
                ],
                [
                    'texto_pregunta' => 'Choose correct verb form: She _____ to the concert last night.',
                    'texto_contexto' => null,
                    'explicacion' => 'Grammar: past tense usage.',
                ],
                [
                    'texto_pregunta' => 'What is the synonym of "important"?',
                    'texto_contexto' => null,
                    'explicacion' => 'Vocabulary: synonym identification.',
                ],
                [
                    'texto_pregunta' => 'Which word completes the sentence: "I am interested _____ learning new languages."?',
                    'texto_contexto' => null,
                    'explicacion' => 'Grammar: prepositions.',
                ],
            ],
        ];

        $template = fake()->randomElement($templatesPorMateria[1]); // Default to first materia

        return [
            'materia_id' => $materiaId,
            'topico_id' => Topico::factory(),
            'texto_pregunta' => $template['texto_pregunta'],
            'texto_contexto' => $template['texto_contexto'],
            'nivel_dificultad' => $dificultad,
            'explicacion' => $template['explicacion'],
            'activo' => true,
        ];
    }

    /**
     * Crea una pregunta para Lectura Crítica.
     */
    public function lecturaCritica(): static
    {
        return $this->state(fn (array $attributes): array => [
            'texto_pregunta' => fake()->randomElement([
                'De acuerdo con el texto anterior, ¿cuál es la idea principal que el autor expone?',
                '¿Qué función cumple la expresión subrayada en el contexto del texto?',
                '¿Cuál de los siguientes enunciados resume mejor el contenido del fragmento?',
                'Según el autor, ¿qué consecuencia directa trae la situación descrita?',
            ]),
        ]);
    }

    /**
     * Crea una pregunta para Matemáticas.
     */
    public function matematicas(): static
    {
        return $this->state(fn (array $attributes): array => [
            'texto_pregunta' => fake()->randomElement([
                'Si f(x) = 2x² - 3x + 5, ¿cuál es el valor de f(4)?',
                '¿Cuál es la solución de la ecuación 3x + 7 = 22?',
                'En un triángulo rectángulo, si un cateto mide 3 cm y el otro 4 cm, ¿cuál es la longitud de la hipotenusa?',
                '¿Cuál es la probabilidad de obtener un número par al lanzar un dado estándar de 6 caras?',
            ]),
        ]);
    }

    /**
     * Crea una pregunta para Sociales y Ciudadanas.
     */
    public function sociales(): static
    {
        return $this->state(fn (array $attributes): array => [
            'texto_pregunta' => fake()->randomElement([
                '¿Cuál fue el principal objetivo de la independencia de Colombia?',
                '¿Qué departamento colombiano se encuentra en la región Caribe?',
                '¿Cuál de las siguientes es una garantía constitucional en Colombia?',
                '¿Qué derecho fundamental protege la libertad de expresión?',
            ]),
        ]);
    }

    /**
     * Crea una pregunta para Ciencias Naturales.
     */
    public function cienciasNaturales(): static
    {
        return $this->state(fn (array $attributes): array => [
            'texto_pregunta' => fake()->randomElement([
                '¿Qué orgánulo celular es responsable de la producción de energía?',
                '¿Cuál es la fórmula química del agua?',
                '¿Qué ley física establece la relación entre fuerza, masa y aceleración?',
                '¿Qué proceso es fundamental para la fotosíntesis?',
            ]),
        ]);
    }

    /**
     * Crea una pregunta para Inglés.
     */
    public function ingles(): static
    {
        return $this->state(fn (array $attributes): array => [
            'texto_pregunta' => fake()->randomElement([
                'Read following text and answer: What is main purpose of the text?',
                'Choose correct verb form: She _____ to the concert last night.',
                'What is the synonym of "important"?',
                'Which word completes the sentence: "I am interested _____ learning new languages."?',
            ]),
        ]);
    }

    /**
     * Crea una pregunta con dificultad Fácil.
     */
    public function facil(): static
    {
        return $this->state(fn (array $attributes): array => [
            'nivel_dificultad' => NivelDificultad::Facil,
        ]);
    }

    /**
     * Crea una pregunta con dificultad Media.
     */
    public function medio(): static
    {
        return $this->state(fn (array $attributes): array => [
            'nivel_dificultad' => NivelDificultad::Medio,
        ]);
    }

    /**
     * Crea una pregunta con dificultad Difícil.
     */
    public function dificil(): static
    {
        return $this->state(fn (array $attributes): array => [
            'nivel_dificultad' => NivelDificultad::Dificil,
        ]);
    }

    /**
     * Marca la pregunta como inactiva.
     */
    public function inactiva(): static
    {
        return $this->state(fn (array $attributes): array => [
            'activo' => false,
        ]);
    }
}
