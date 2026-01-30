<?php

namespace Database\Seeders;

use App\Models\Materia;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MateriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('materias')->truncate();

        $materias = [
            [
                'nombre' => 'Lectura Crítica',
                'slug' => 'lectura-critica',
                'descripcion' => 'Comprensió lectora, análisis de textos y capacidad de interpretar diferentes tipos de información.',
                'icono' => 'book-open',
                'cantidad_preguntas' => 41,
                'tiempo_limite' => 60,
                'orden_visualizacion' => 1,
                'activo' => true,
            ],
            [
                'nombre' => 'Matemáticas',
                'slug' => 'matematicas',
                'descripcion' => 'Álgebra, geometría, estadística y resolución de problemas matemáticos.',
                'icono' => 'calculator',
                'cantidad_preguntas' => 50,
                'tiempo_limite' => 80,
                'orden_visualizacion' => 2,
                'activo' => true,
            ],
            [
                'nombre' => 'Sociales y Ciudadanas',
                'slug' => 'sociales',
                'descripcion' => 'Historia, geografía, constitución y cívica.',
                'icono' => 'globe',
                'cantidad_preguntas' => 50,
                'tiempo_limite' => 70,
                'orden_visualizacion' => 3,
                'activo' => true,
            ],
            [
                'nombre' => 'Ciencias Naturales',
                'slug' => 'ciencias-naturales',
                'descripcion' => 'Biología, química y física.',
                'icono' => 'flask',
                'cantidad_preguntas' => 50,
                'tiempo_limite' => 60,
                'orden_visualizacion' => 4,
                'activo' => true,
            ],
            [
                'nombre' => 'Inglés',
                'slug' => 'ingles',
                'descripcion' => 'Comprensión de lectura, gramática y vocabulario en inglés.',
                'icono' => 'language',
                'cantidad_preguntas' => 50,
                'tiempo_limite' => 50,
                'orden_visualizacion' => 5,
                'activo' => true,
            ],
        ];

        Materia::insert($materias);
    }
}
