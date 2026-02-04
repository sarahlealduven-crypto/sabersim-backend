<?php

namespace Database\Seeders;

use App\Models\Materia;
use App\Models\Topico;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TopicoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('topicos')->truncate();

        $materias = Materia::all()->keyBy('slug');

        $topicos = [];

        // Lectura Crítica
        if ($materias->has('lectura-critica')) {
            $materiaId = $materias['lectura-critica']->id;
            $topicos = array_merge($topicos, [
                [
                    'materia_id' => $materiaId,
                    'nombre' => 'Comprensión de Lectura',
                    'slug' => 'comprension-lectura',
                    'descripcion' => 'Capacidad para entender, interpretar y analizar textos narrativos, expositivos y argumentativos.',
                ],
                [
                    'materia_id' => $materiaId,
                    'nombre' => 'Análisis de Textos Literarios',
                    'slug' => 'analisis-textos-literarios',
                    'descripcion' => 'Estudio y comprensión de obras literarias, figuras literarias y elementos narrativos.',
                ],
                [
                    'materia_id' => $materiaId,
                    'nombre' => 'Identificación de Información',
                    'slug' => 'identificacion-informacion',
                    'descripcion' => 'Localización y extracción de información específica en diferentes tipos de textos.',
                ],
                [
                    'materia_id' => $materiaId,
                    'nombre' => 'Inferencias y Deducciones',
                    'slug' => 'inferencias-deducciones',
                    'descripcion' => 'Capacidad para sacar conclusiones implícitas a partir de la información del texto.',
                ],
            ]);
        }

        // Matemáticas
        if ($materias->has('matematicas')) {
            $materiaId = $materias['matematicas']->id;
            $topicos = array_merge($topicos, [
                [
                    'materia_id' => $materiaId,
                    'nombre' => 'Aritmética',
                    'slug' => 'aritmetica',
                    'descripcion' => 'Operaciones con números enteros, fracciones, decimales y porcentajes.',
                ],
                [
                    'materia_id' => $materiaId,
                    'nombre' => 'Álgebra',
                    'slug' => 'algebra',
                    'descripcion' => 'Expresiones algebraicas, ecuaciones, inecuaciones y sistemas de ecuaciones.',
                ],
                [
                    'materia_id' => $materiaId,
                    'nombre' => 'Geometría',
                    'slug' => 'geometria',
                    'descripcion' => 'Figuras geométricas, áreas, volúmenes, teoremas y construcciones.',
                ],
                [
                    'materia_id' => $materiaId,
                    'nombre' => 'Estadística y Probabilidad',
                    'slug' => 'estadistica-probabilidad',
                    'descripcion' => 'Análisis de datos, medidas de tendencia central, gráficos y cálculo de probabilidades.',
                ],
                [
                    'materia_id' => $materiaId,
                    'nombre' => 'Razonamiento Cuantitativo',
                    'slug' => 'razonamiento-cuantitativo',
                    'descripcion' => 'Resolución de problemas y aplicación de conceptos matemáticos en situaciones reales.',
                ],
            ]);
        }

        // Sociales y Ciudadanas
        if ($materias->has('sociales')) {
            $materiaId = $materias['sociales']->id;
            $topicos = array_merge($topicos, [
                [
                    'materia_id' => $materiaId,
                    'nombre' => 'Historia de Colombia',
                    'slug' => 'historia-colombia',
                    'descripcion' => 'Eventos históricos, procesos políticos, sociales y económicos de Colombia.',
                ],
                [
                    'materia_id' => $materiaId,
                    'nombre' => 'Geografía de Colombia',
                    'slug' => 'geografia-colombia',
                    'descripcion' => 'Relieve, clima, hidrografía, recursos naturales y división político-administrativa.',
                ],
                [
                    'materia_id' => $materiaId,
                    'nombre' => 'Constitución Política',
                    'slug' => 'constitucion-politica',
                    'descripcion' => 'Derechos fundamentales, organización del Estado, ramas del poder y ciudadanía.',
                ],
                [
                    'materia_id' => $materiaId,
                    'nombre' => 'Historia Universal',
                    'slug' => 'historia-universal',
                    'descripcion' => 'Principales civilizaciones, guerras mundiales, Guerra Fría y mundo contemporáneo.',
                ],
                [
                    'materia_id' => $materiaId,
                    'nombre' => 'Economía',
                    'slug' => 'economia',
                    'descripcion' => 'Conceptos económicos, mercado, inflación, PIB y desarrollo económico.',
                ],
            ]);
        }

        // Ciencias Naturales
        if ($materias->has('ciencias-naturales')) {
            $materiaId = $materias['ciencias-naturales']->id;
            $topicos = array_merge($topicos, [
                [
                    'materia_id' => $materiaId,
                    'nombre' => 'Biología',
                    'slug' => 'biologia',
                    'descripcion' => 'Células, genética, evolución, ecología y sistemas del cuerpo humano.',
                ],
                [
                    'materia_id' => $materiaId,
                    'nombre' => 'Química',
                    'slug' => 'quimica',
                    'descripcion' => 'Tabla periódica, enlaces químicos, reacciones, ácidos y bases.',
                ],
                [
                    'materia_id' => $materiaId,
                    'nombre' => 'Física',
                    'slug' => 'fisica',
                    'descripcion' => 'Cinemática, dinámica, energía, electricidad, magnetismo y ondas.',
                ],
                [
                    'materia_id' => $materiaId,
                    'nombre' => 'Ciencias Ambientales',
                    'slug' => 'ciencias-ambientales',
                    'descripcion' => 'Medio ambiente, contaminación, cambio climático y sostenibilidad.',
                ],
            ]);
        }

        // Inglés
        if ($materias->has('ingles')) {
            $materiaId = $materias['ingles']->id;
            $topicos = array_merge($topicos, [
                [
                    'materia_id' => $materiaId,
                    'nombre' => 'Reading Comprehension',
                    'slug' => 'reading-comprehension',
                    'descripcion' => 'Comprensión de textos en inglés: identificación de ideas principales y detalles.',
                ],
                [
                    'materia_id' => $materiaId,
                    'nombre' => 'Grammar',
                    'slug' => 'grammar',
                    'descripcion' => 'Estructuras gramaticales: verb tenses, conditionals, passive voice y más.',
                ],
                [
                    'materia_id' => $materiaId,
                    'nombre' => 'Vocabulary',
                    'slug' => 'vocabulary',
                    'descripcion' => 'Vocabulario en contexto: sinónimos, antónimos y expresiones idiomáticas.',
                ],
            ]);
        }

        Topico::insert($topicos);
    }
}
