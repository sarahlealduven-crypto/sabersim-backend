<?php

namespace Database\Seeders;

use App\Models\EstadisticaUsuario;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstadisticaUsuarioSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('estadisticas_usuarios')->truncate();

        $users = User::all();
        $materias = \App\Models\Materia::all();

        foreach ($users as $user) {
            foreach ($materias as $materia) {
                // Only create statistics for users who have taken exams in this subject
                $examenesUsuario = \App\Models\Examen::where('user_id', $user->id)
                    ->whereHas('seccionesExamen', function ($query) use ($materia) {
                        $query->where('materia_id', $materia->id);
                    })
                    ->where('estado', 'completado')
                    ->get();

                if ($examenesUsuario->count() > 0) {
                    $totalExamenes = $examenesUsuario->count();
                    $totalPreguntas = 0;
                    $respuestasCorrectas = 0;
                    $mejorPuntaje = 0;
                    $tiempoTotal = 0;

                    foreach ($examenesUsuario as $examen) {
                        $seccion = $examen->seccionesExamen()->where('materia_id', $materia->id)->first();

                        if ($seccion) {
                            $totalPreguntas += $seccion->total_preguntas;
                            $respuestasCorrectas += $seccion->respuestas_correctas;
                            $mejorPuntaje = max($mejorPuntaje, $seccion->puntaje);
                            $tiempoTotal += $seccion->tiempo_gastado;
                        }
                    }

                    $puntajePromedio = round(($respuestasCorrectas / $totalPreguntas) * 10, 2);

                    EstadisticaUsuario::create([
                        'user_id' => $user->id,
                        'materia_id' => $materia->id,
                        'total_examenes' => $totalExamenes,
                        'total_preguntas_respondidas' => $totalPreguntas,
                        'respuestas_correctas' => $respuestasCorrectas,
                        'puntaje_promedio' => $puntajePromedio,
                        'mejor_puntaje' => $mejorPuntaje,
                        'tiempo_total_gastado' => $tiempoTotal,
                        'fecha_ultimo_examen' => $examenesUsuario->last()->fecha_completado,
                    ]);
                }
            }
        }
    }
}
