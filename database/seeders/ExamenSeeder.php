<?php

namespace Database\Seeders;

use App\Enums\EstadoExamen;
use App\Enums\TipoExamen;
use App\Models\Examen;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExamenSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('examenes')->truncate();
        DB::table('secciones_examen')->truncate();

        $users = User::all();

        foreach ($users as $user) {
            // Create 2-3 completed exams per user
            $numExamenesCompletados = rand(2, 3);

            for ($i = 0; $i < $numExamenesCompletados; $i++) {
                $fechaInicio = now()->subDays(rand(1, 30))->subHours(rand(1, 10));
                $tiempoGastado = rand(1800, 5400); // 30-90 minutes in seconds

                $examen = Examen::create([
                    'user_id' => $user->id,
                    'tipo_examen' => rand(0, 1) === 0 ? TipoExamen::Completo : TipoExamen::PorMateria,
                    'estado' => EstadoExamen::Completado,
                    'fecha_inicio' => $fechaInicio,
                    'fecha_completado' => $fechaInicio->copy()->addSeconds($tiempoGastado),
                    'puntaje_total' => round(rand(40, 98) / 10, 2), // 4.0 to 9.8
                    'tiempo_gastado' => $tiempoGastado,
                ]);

                // Create 1-3 sections per exam
                $numSecciones = rand(1, 3);
                $materiasIds = \App\Models\Materia::inRandomOrder()->take($numSecciones)->pluck('id');

                foreach ($materiasIds as $materiaId) {
                    $respuestasCorrectas = rand(5, 20);
                    $totalPreguntas = rand(20, 30);
                    $puntaje = round(($respuestasCorrectas / $totalPreguntas) * 10, 2);

                    \App\Models\SeccionExamen::create([
                        'examen_id' => $examen->id,
                        'materia_id' => $materiaId,
                        'puntaje' => $puntaje,
                        'respuestas_correctas' => $respuestasCorrectas,
                        'total_preguntas' => $totalPreguntas,
                        'tiempo_gastado' => rand(300, 1800), // 5-30 minutes
                    ]);
                }
            }

            // Create 1-2 exams in progress per user
            $numExamenesEnProgreso = rand(0, 2);

            for ($i = 0; $i < $numExamenesEnProgreso; $i++) {
                $examen = Examen::create([
                    'user_id' => $user->id,
                    'tipo_examen' => rand(0, 1) === 0 ? TipoExamen::Completo : TipoExamen::PorMateria,
                    'estado' => EstadoExamen::EnProgreso,
                    'fecha_inicio' => now()->subMinutes(rand(5, 60)),
                    'fecha_completado' => null,
                    'puntaje_total' => null,
                    'tiempo_gastado' => rand(300, 3600), // 5-60 minutes so far
                ]);

                // Create 1 section for in-progress exams
                $materiaId = \App\Models\Materia::inRandomOrder()->first()->id;

                \App\Models\SeccionExamen::create([
                    'examen_id' => $examen->id,
                    'materia_id' => $materiaId,
                    'puntaje' => null,
                    'respuestas_correctas' => rand(0, 5),
                    'total_preguntas' => rand(10, 20),
                    'tiempo_gastado' => rand(300, 1800),
                ]);
            }
        }
    }
}
