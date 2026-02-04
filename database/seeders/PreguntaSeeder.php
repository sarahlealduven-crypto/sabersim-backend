<?php

namespace Database\Seeders;

use App\Enums\NivelDificultad;
use App\Models\Materia;
use App\Models\Pregunta;
use App\Models\Topico;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PreguntaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('preguntas')->truncate();
        DB::table('opciones_respuesta')->truncate();

        $materias = Materia::all()->keyBy('slug');
        $topicos = Topico::all()->keyBy('slug');

        $preguntas = [];

        // LECTURA CRÍTICA - Comprensión de Lectura
        if ($materias->has('lectura-critica') && $topicos->has('comprension-lectura')) {
            $materiaId = $materias['lectura-critica']->id;
            $topicoId = $topicos['comprension-lectura']->id;

            $preguntas = array_merge($preguntas, [
                [
                    'materia_id' => $materiaId,
                    'topico_id' => $topicoId,
                    'texto_pregunta' => 'Según el texto, cuál es el objetivo principal del autor?',
                    'texto_contexto' => 'La biodiversidad en Colombia es una de las mayores del mundo, con más de 50.000 especies de plantas y animales. Sin embargo, la deforestación y el cambio climático amenazan este patrimonio natural. Es fundamental implementar políticas de conservación que protejan los ecosistemas y fomenten el desarrollo sostenible.',
                    'nivel_dificultad' => NivelDificultad::Medio,
                    'explicacion' => 'El autor busca sensibilizar sobre la importancia de conservar la biodiversidad en Colombia ante amenazas como la deforestación y el cambio climático.',
                    'activo' => true,
                    'opciones' => [
                        ['A', 'Desarrollar la industria maderera', false],
                        ['B', 'Proteger la biodiversidad mediante políticas de conservación', true],
                        ['C', 'Eliminar todas las áreas protegidas', false],
                        ['D', 'Fomentar la urbanización de zonas rurales', false],
                    ],
                ],
                [
                    'materia_id' => $materiaId,
                    'topico_id' => $topicoId,
                    'texto_pregunta' => 'Qué conclusión se puede derivar del texto anterior?',
                    'texto_contexto' => 'El aprendizaje de idiomas desde temprana edad tiene múltiples beneficios cognitivos: mejora la memoria, desarrolla habilidades multitarea y aumenta la capacidad de resolución de problemas. Además, los niños bilingües tienden a tener mayor flexibilidad mental y mejor desempeño académico.',
                    'nivel_dificultad' => NivelDificultad::Facil,
                    'explicacion' => 'El texto evidencia correlación entre aprendizaje temprano de idiomas y mejoras cognitivas, derivando en mejor desempeño académico.',
                    'activo' => true,
                    'opciones' => [
                        ['A', 'Los niños bilingües tienen mayor rendimiento académico', true],
                        ['B', 'Aprender idiomas es inútil después de los 20 años', false],
                        ['C', 'Solo se debe aprender un idioma extranjero', false],
                        ['D', 'El aprendizaje de idiomas afecta negativamente la memoria', false],
                    ],
                ],
            ]);

            // Análisis de Textos Literarios
            if ($topicos->has('analisis-textos-literarios')) {
                $topicoId = $topicos['analisis-textos-literarios']->id;
                $preguntas = array_merge($preguntas, [
                    [
                        'materia_id' => $materiaId,
                        'topico_id' => $topicoId,
                        'texto_pregunta' => 'Qué figura literaria predomina en el siguiente fragmento: "El mar susurraba canciones de eternidad"?',
                        'texto_contexto' => null,
                        'nivel_dificultad' => NivelDificultad::Dificil,
                        'explicacion' => 'La personificación es una figura retórica que atribuye cualidades humanas a objetos o seres inanimados. En este caso, el mar "susurra", característica humana.',
                        'activo' => true,
                        'opciones' => [
                            ['A', 'Metáfora', false],
                            ['B', 'Personificación', true],
                            ['C', 'Hipérbole', false],
                            ['D', 'Paradoja', false],
                        ],
                    ],
                ]);
            }
        }

        // MATEMÁTICAS - Aritmética
        if ($materias->has('matematicas') && $topicos->has('aritmetica')) {
            $materiaId = $materias['matematicas']->id;
            $topicoId = $topicos['aritmetica']->id;

            $preguntas = array_merge($preguntas, [
                [
                    'materia_id' => $materiaId,
                    'topico_id' => $topicoId,
                    'texto_pregunta' => 'Una tienda ofrece un descuento del 15% sobre un artículo de $80.000. Cuánto debe pagar el cliente?',
                    'texto_contexto' => null,
                    'nivel_dificultad' => NivelDificultad::Facil,
                    'explicacion' => 'Descuento = 80.000 × 0.15 = $12.000. Precio final = 80.000 - 12.000 = $68.000.',
                    'activo' => true,
                    'opciones' => [
                        ['A', '$12.000', false],
                        ['B', '$68.000', true],
                        ['C', '$65.000', false],
                        ['D', '$70.000', false],
                    ],
                ],
                [
                    'materia_id' => $materiaId,
                    'topico_id' => $topicoId,
                    'texto_pregunta' => 'Si 3 obreros pueden construir una casa en 24 días, cuántos días tardarán 6 obreros?',
                    'texto_contexto' => null,
                    'nivel_dificultad' => NivelDificultad::Medio,
                    'explicacion' => 'Es una proporción inversa: más obreros, menos tiempo. 3 × 24 = 72 obreros-día. 72 ÷ 6 = 12 días.',
                    'activo' => true,
                    'opciones' => [
                        ['A', '12 días', true],
                        ['B', '48 días', false],
                        ['C', '8 días', false],
                        ['D', '6 días', false],
                    ],
                ],
            ]);

            // Álgebra
            if ($topicos->has('algebra')) {
                $topicoId = $topicos['algebra']->id;
                $preguntas = array_merge($preguntas, [
                    [
                        'materia_id' => $materiaId,
                        'topico_id' => $topicoId,
                        'texto_pregunta' => 'Cuál es el valor de x en la ecuación 2x + 8 = 20?',
                        'texto_contexto' => null,
                        'nivel_dificultad' => NivelDificultad::Facil,
                        'explicacion' => '2x = 20 - 8, 2x = 12, x = 12/2, x = 6.',
                        'activo' => true,
                        'opciones' => [
                            ['A', '4', false],
                            ['B', '6', true],
                            ['C', '8', false],
                            ['D', '10', false],
                        ],
                    ],
                ]);
            }

            // Geometría
            if ($topicos->has('geometria')) {
                $topicoId = $topicos['geometria']->id;
                $preguntas = array_merge($preguntas, [
                    [
                        'materia_id' => $materiaId,
                        'topico_id' => $topicoId,
                        'texto_pregunta' => 'Cuál es el área de un triángulo con base de 10 cm y altura de 6 cm?',
                        'texto_contexto' => null,
                        'nivel_dificultad' => NivelDificultad::Facil,
                        'explicacion' => 'Área = (base × altura) / 2 = (10 × 6) / 2 = 60 / 2 = 30 cm².',
                        'activo' => true,
                        'opciones' => [
                            ['A', '16 cm²', false],
                            ['B', '30 cm²', true],
                            ['C', '60 cm²', false],
                            ['D', '32 cm²', false],
                        ],
                    ],
                ]);
            }

            // Estadística
            if ($topicos->has('estadistica-probabilidad')) {
                $topicoId = $topicos['estadistica-probabilidad']->id;
                $preguntas = array_merge($preguntas, [
                    [
                        'materia_id' => $materiaId,
                        'topico_id' => $topicoId,
                        'texto_pregunta' => 'Cuál es la probabilidad de obtener un número par al lanzar un dado de 6 caras?',
                        'texto_contexto' => null,
                        'nivel_dificultad' => NivelDificultad::Facil,
                        'explicacion' => 'Casos favorables (números pares): 2, 4, 6 = 3. Total de casos: 6. Probabilidad = 3/6 = 1/2 = 50%.',
                        'activo' => true,
                        'opciones' => [
                            ['A', '1/6', false],
                            ['B', '1/3', false],
                            ['C', '1/2', true],
                            ['D', '2/3', false],
                        ],
                    ],
                ]);
            }
        }

        // SOCIALES Y CIUDADANAS - Historia de Colombia
        if ($materias->has('sociales') && $topicos->has('historia-colombia')) {
            $materiaId = $materias['sociales']->id;
            $topicoId = $topicos['historia-colombia']->id;

            $preguntas = array_merge($preguntas, [
                [
                    'materia_id' => $materiaId,
                    'topico_id' => $topicoId,
                    'texto_pregunta' => 'Qué acontecimiento marcó el inicio de la independencia de Colombia el 20 de julio de 1810?',
                    'texto_contexto' => 'El Grito de Independencia fue un episodio crucial en la historia de Colombia. Un grupo de criollos, liderados por Camilo Torres, aprovechó la visita del comisionado real Antonio Villavicencio para desconocer la autoridad del virrey Amar y Borbón.',
                    'nivel_dificultad' => NivelDificultad::Medio,
                    'explicacion' => 'El 20 de julio de 1810 se creó la Junta de Gobierno en Santa Fe de Bogotá, iniciando el proceso independentista.',
                    'activo' => true,
                    'opciones' => [
                        ['A', 'La Batalla de Boyacá', false],
                        ['B', 'La creación de la Junta de Gobierno', true],
                        ['C', 'El Congreso de Angostura', false],
                        ['D', 'La Batalla del Pantano de Vargas', false],
                    ],
                ],
                [
                    'materia_id' => $materiaId,
                    'topico_id' => $topicoId,
                    'texto_pregunta' => 'Quién fue el presidente que lideró la violencia en Colombia durante el periodo conocido como La Violencia (1948-1958)?',
                    'texto_contexto' => 'El 9 de abril de 1948 fue asesinado Jorge Eliécer Gaitán, líder liberal. Este evento desencadenó un periodo de enfrentamientos entre liberales y conservadores conocido como La Violencia, que causó más de 200.000 muertos.',
                    'nivel_dificultad' => NivelDificultad::Medio,
                    'explicacion' => 'La Violencia ocurrió durante la presidencia de Laureano Gómez y luego de Gustavo Rojas Pinilla.',
                    'activo' => true,
                    'opciones' => [
                        ['A', 'Alfonso López Pumarejo', false],
                        ['B', 'Laureano Gómez', true],
                        ['C', 'Carlos Lleras Restrepo', false],
                        ['D', 'Misael Pastrana', false],
                    ],
                ],
            ]);

            // Constitución Política
            if ($topicos->has('constitucion-politica')) {
                $topicoId = $topicos['constitucion-politica']->id;
                $preguntas = array_merge($preguntas, [
                    [
                        'materia_id' => $materiaId,
                        'topico_id' => $topicoId,
                        'texto_pregunta' => 'Cuál es el máximo periodo que un presidente puede permanecer en el cargo en Colombia?',
                        'texto_contexto' => 'Según la Constitución Política de 1991, el presidente de la República es elegido por votación popular para un periodo de cuatro años. No puede ser reelegido de inmediato.',
                        'nivel_dificultad' => NivelDificultad::Facil,
                        'explicacion' => 'La Constitución de 1991 establece un periodo de 4 años sin reelección inmediata.',
                        'activo' => true,
                        'opciones' => [
                            ['A', '2 años', false],
                            ['B', '4 años', true],
                            ['C', '6 años', false],
                            ['D', '8 años', false],
                        ],
                    ],
                ]);
            }

            // Geografía
            if ($topicos->has('geografia-colombia')) {
                $topicoId = $topicos['geografia-colombia']->id;
                $preguntas = array_merge($preguntas, [
                    [
                        'materia_id' => $materiaId,
                        'topico_id' => $topicoId,
                        'texto_pregunta' => 'Cuál es la cordillera más alta de Colombia?',
                        'texto_contexto' => 'El sistema andino en Colombia está conformado por tres cordilleras: Occidental, Central y Oriental. La Cordillera Central contiene los picos más altos del país.',
                        'nivel_dificultad' => NivelDificultad::Medio,
                        'explicacion' => 'La Cordillera Central es la más alta y contiene el Nevado del Huila y el nevado del Tolima.',
                        'activo' => true,
                        'opciones' => [
                            ['A', 'Cordillera Occidental', false],
                            ['B', 'Cordillera Central', true],
                            ['C', 'Cordillera Oriental', false],
                            ['D', 'Serranía del Perijá', false],
                        ],
                    ],
                ]);
            }
        }

        // CIENCIAS NATURALES - Biología
        if ($materias->has('ciencias-naturales') && $topicos->has('biologia')) {
            $materiaId = $materias['ciencias-naturales']->id;
            $topicoId = $topicos['biologia']->id;

            $preguntas = array_merge($preguntas, [
                [
                    'materia_id' => $materiaId,
                    'topico_id' => $topicoId,
                    'texto_pregunta' => 'Cuál es la función principal de los cloroplastos en las células vegetales?',
                    'texto_contexto' => 'Los cloroplastos son organelos que contienen clorofila, un pigmento verde que captura la energía solar. Esta energía se utiliza para convertir dióxido de carbono y agua en glucosa mediante el proceso de fotosíntesis.',
                    'nivel_dificultad' => NivelDificultad::Facil,
                    'explicacion' => 'Los cloroplastos realizan la fotosíntesis, produciendo glucosa a partir de energía solar, CO₂ y agua.',
                    'activo' => true,
                    'opciones' => [
                        ['A', 'Almacenar agua', false],
                        ['B', 'Realizar la fotosíntesis', true],
                        ['C', 'Proteger la célula', false],
                        ['D', 'Eliminar desechos', false],
                    ],
                ],
                [
                    'materia_id' => $materiaId,
                    'topico_id' => $topicoId,
                    'texto_pregunta' => 'Cuál es el proceso mediante el cual las plantas producen su propio alimento?',
                    'texto_contexto' => null,
                    'nivel_dificultad' => NivelDificultad::Facil,
                    'explicacion' => 'La fotosíntesis es el proceso mediante el cual las plantas convierten energía solar en glucosa.',
                    'activo' => true,
                    'opciones' => [
                        ['A', 'Respiración celular', false],
                        ['B', 'Fotosíntesis', true],
                        ['C', 'Digestión', false],
                        ['D', 'Fermentación', false],
                    ],
                ],
            ]);

            // Química
            if ($topicos->has('quimica')) {
                $topicoId = $topicos['quimica']->id;
                $preguntas = array_merge($preguntas, [
                    [
                        'materia_id' => $materiaId,
                        'topico_id' => $topicoId,
                        'texto_pregunta' => 'Cuál es el símbolo químico del oxígeno?',
                        'texto_contexto' => null,
                        'nivel_dificultad' => NivelDificultad::Facil,
                        'explicacion' => 'El oxígeno es un elemento químico con símbolo O y número atómico 8.',
                        'activo' => true,
                        'opciones' => [
                            ['A', 'O', true],
                            ['B', 'Ox', false],
                            ['C', 'Oxg', false],
                            ['D', 'Oxy', false],
                        ],
                    ],
                    [
                        'materia_id' => $materiaId,
                        'topico_id' => $topicoId,
                        'texto_pregunta' => 'Cuál es el pH característico de una sustancia neutra?',
                        'texto_contexto' => 'La escala de pH va de 0 a 14. Valores menores a 7 indican acidez, mayores a 7 indican alcalinidad, y 7 es neutral.',
                        'nivel_dificultad' => NivelDificultad::Facil,
                        'explicacion' => 'El pH 7 es neutro, como el agua pura.',
                        'activo' => true,
                        'opciones' => [
                            ['A', '0', false],
                            ['B', '7', true],
                            ['C', '14', false],
                            ['D', '3.5', false],
                        ],
                    ],
                ]);
            }

            // Física
            if ($topicos->has('fisica')) {
                $topicoId = $topicos['fisica']->id;
                $preguntas = array_merge($preguntas, [
                    [
                        'materia_id' => $materiaId,
                        'topico_id' => $topicoId,
                        'texto_pregunta' => 'Un objeto se mueve a velocidad constante. Qué se puede afirmar sobre su aceleración?',
                        'texto_contexto' => 'La aceleración es el cambio de velocidad por unidad de tiempo. Si la velocidad es constante, no hay cambio.',
                        'nivel_dificultad' => NivelDificultad::Medio,
                        'explicacion' => 'Si la velocidad es constante, la aceleración es cero.',
                        'activo' => true,
                        'opciones' => [
                            ['A', 'Es positiva', false],
                            ['B', 'Es negativa', false],
                            ['C', 'Es cero', true],
                            ['D', 'No se puede determinar', false],
                        ],
                    ],
                ]);
            }

            // Ciencias Ambientales
            if ($topicos->has('ciencias-ambientales')) {
                $topicoId = $topicos['ciencias-ambientales']->id;
                $preguntas = array_merge($preguntas, [
                    [
                        'materia_id' => $materiaId,
                        'topico_id' => $topicoId,
                        'texto_pregunta' => 'Qué gas es principalmente responsable del efecto invernadero?',
                        'texto_contexto' => 'El efecto invernadero es un fenómeno natural que mantiene la Tierra caliente. Sin embargo, el exceso de ciertos gases, producto de la actividad humana, está intensificando este efecto y provocando el cambio climático.',
                        'nivel_dificultad' => NivelDificultad::Facil,
                        'explicacion' => 'El dióxido de carbono (CO₂) es el principal gas de efecto invernadero.',
                        'activo' => true,
                        'opciones' => [
                            ['A', 'Oxígeno (O₂)', false],
                            ['B', 'Nitrógeno (N₂)', false],
                            ['C', 'Dióxido de carbono (CO₂)', true],
                            ['D', 'Argón (Ar)', false],
                        ],
                    ],
                ]);
            }
        }

        // INGLÉS - Reading Comprehension
        if ($materias->has('ingles') && $topicos->has('reading-comprehension')) {
            $materiaId = $materias['ingles']->id;
            $topicoId = $topicos['reading-comprehension']->id;

            $preguntas = array_merge($preguntas, [
                [
                    'materia_id' => $materiaId,
                    'topico_id' => $topicoId,
                    'texto_pregunta' => 'According to the text, what is the main advantage of renewable energy?',
                    'texto_contexto' => 'Renewable energy sources, such as solar and wind power, are becoming increasingly popular. They do not produce greenhouse gases and are inexhaustible, unlike fossil fuels. Additionally, they help reduce dependence on imported energy and create local jobs.',
                    'nivel_dificultad' => NivelDificultad::Medio,
                    'explicacion' => 'The text highlights that renewable energy does not produce greenhouse gases and is inexhaustible.',
                    'activo' => true,
                    'opciones' => [
                        ['A', 'It is cheaper than fossil fuels', false],
                        ['B', 'It does not produce greenhouse gases', true],
                        ['C', 'It requires less maintenance', false],
                        ['D', 'It is available everywhere', false],
                    ],
                ],
                [
                    'materia_id' => $materiaId,
                    'topico_id' => $topicoId,
                    'texto_pregunta' => 'What does the word "inexhaustible" mean in the context of the text?',
                    'texto_contexto' => null,
                    'nivel_dificultad' => NivelDificultad::Dificil,
                    'explicacion' => 'Inexhaustible means "impossible to use up completely" or "endless".',
                    'activo' => true,
                    'opciones' => [
                        ['A', 'Very expensive', false],
                        ['B', 'Impossible to use up completely', true],
                        ['C', 'Dangerous for the environment', false],
                        ['D', 'Difficult to find', false],
                    ],
                ],
            ]);

            // Grammar
            if ($topicos->has('grammar')) {
                $topicoId = $topicos['grammar']->id;
                $preguntas = array_merge($preguntas, [
                    [
                        'materia_id' => $materiaId,
                        'topico_id' => $topicoId,
                        'texto_pregunta' => 'Complete the sentence: "If I ___ rich, I would travel around the world."',
                        'texto_contexto' => null,
                        'nivel_dificultad' => NivelDificultad::Facil,
                        'explicacion' => 'This is a second conditional sentence. Structure: If + past simple, would + base verb. The verb "to be" uses "were" for all subjects.',
                        'activo' => true,
                        'opciones' => [
                            ['A', 'am', false],
                            ['B', 'was', false],
                            ['C', 'were', true],
                            ['D', 'will be', false],
                        ],
                    ],
                ]);
            }

            // Vocabulary
            if ($topicos->has('vocabulary')) {
                $topicoId = $topicos['vocabulary']->id;
                $preguntas = array_merge($preguntas, [
                    [
                        'materia_id' => $materiaId,
                        'topico_id' => $topicoId,
                        'texto_pregunta' => 'Choose the word that is a synonym of "happy":',
                        'texto_contexto' => null,
                        'nivel_dificultad' => NivelDificultad::Facil,
                        'explicacion' => '"Joyful" means feeling, expressing, or causing great pleasure and happiness - a synonym of happy.',
                        'activo' => true,
                        'opciones' => [
                            ['A', 'Sad', false],
                            ['B', 'Angry', false],
                            ['C', 'Joyful', true],
                            ['D', 'Tired', false],
                        ],
                    ],
                ]);
            }
        }

        // Insert all questions
        foreach ($preguntas as $pregunta) {
            $opciones = $pregunta['opciones'];
            unset($pregunta['opciones']);

            $nuevaPregunta = Pregunta::create($pregunta);

            foreach ($opciones as $opcion) {
                \App\Models\OpcionRespuesta::create([
                    'pregunta_id' => $nuevaPregunta->id,
                    'letra_opcion' => $opcion[0],
                    'texto_opcion' => $opcion[0] . '. ' . $opcion[1],
                    'es_correcta' => $opcion[2],
                ]);
            }
        }
    }
}
