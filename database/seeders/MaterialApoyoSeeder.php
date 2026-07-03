<?php

namespace Database\Seeders;

use App\Models\Materia;
use App\Models\MaterialApoyo;
use Illuminate\Database\Seeder;

class MaterialApoyoSeeder extends Seeder
{
    public function run(): void
    {
        MaterialApoyo::query()->delete();

        $materias = Materia::query()->get()->keyBy('slug');

        $materiales = [
            [
                'materia_slug' => 'lectura-critica',
                'titulo' => 'Guía para resolver preguntas de lectura crítica',
                'slug' => 'guia-preguntas-lectura-critica',
                'descripcion' => 'Estrategias para reconocer tesis, intención comunicativa y opciones distractoras.',
                'tipo' => MaterialApoyo::TIPO_YOUTUBE,
                'source_url' => 'https://www.youtube.com/watch?v=jjO21znJdiE',
                'thumbnail_url' => 'https://img.youtube.com/vi/jjO21znJdiE/hqdefault.jpg',
                'duracion' => 'Video',
                'orden_visualizacion' => 1,
            ],
            [
                'materia_slug' => 'matematicas',
                'titulo' => 'Razonamiento cuantitativo: ejercicios explicados',
                'slug' => 'razonamiento-cuantitativo-ejercicios-explicados',
                'descripcion' => 'Resolución paso a paso de ejercicios tipo Saber para fortalecer análisis numérico.',
                'tipo' => MaterialApoyo::TIPO_YOUTUBE,
                'source_url' => 'https://www.youtube.com/watch?v=AvVJBNZlOsY',
                'thumbnail_url' => 'https://img.youtube.com/vi/AvVJBNZlOsY/hqdefault.jpg',
                'duracion' => 'Video',
                'orden_visualizacion' => 2,
            ],
            [
                'materia_slug' => 'ciencias-naturales',
                'titulo' => 'Ciencias naturales: biología Saber 11',
                'slug' => 'ciencias-naturales-biologia-saber-11',
                'descripcion' => 'Repaso de biología con enfoque en competencias evaluadas por Saber 11.',
                'tipo' => MaterialApoyo::TIPO_YOUTUBE,
                'source_url' => 'https://www.youtube.com/watch?v=SqjBzos_Frc',
                'thumbnail_url' => 'https://img.youtube.com/vi/SqjBzos_Frc/hqdefault.jpg',
                'duracion' => 'Video',
                'orden_visualizacion' => 3,
            ],
            [
                'materia_slug' => 'lectura-critica',
                'titulo' => 'Cuadernillo Saber 11 de lectura crítica',
                'slug' => 'cuadernillo-saber-11-lectura-critica',
                'descripcion' => 'PDF externo en Google Drive para practicar preguntas tipo Saber 11.',
                'tipo' => MaterialApoyo::TIPO_GOOGLE_DRIVE,
                'source_url' => 'https://drive.google.com/file/d/148MvqVppDg7w0x9HJQnadS2OaS2ymiOx/view?usp=sharing',
                'thumbnail_url' => null,
                'duracion' => 'Guía',
                'orden_visualizacion' => 4,
            ],
        ];

        foreach ($materiales as $material) {
            $materiaSlug = $material['materia_slug'];
            unset($material['materia_slug']);

            MaterialApoyo::create([
                ...$material,
                'materia_id' => $materias->get($materiaSlug)?->id,
                'embed_url' => MaterialApoyo::embedUrlFor($material['tipo'], $material['source_url']),
                'activo' => true,
            ]);
        }
    }
}
