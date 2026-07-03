<?php

namespace App\Services;

use App\Enums\EstadoExamen;
use App\Enums\TipoExamen;
use App\Models\Examen;
use App\Models\Materia;
use App\Models\Pregunta;
use App\Models\SeccionExamen;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ExamenService
{
    public function generarExamen(User $user, TipoExamen $tipo, ?int $materiaId = null): Examen
    {
        $materias = $tipo === TipoExamen::PorMateria
            ? Materia::where('id', $materiaId)->where('activo', true)->get()
            : Materia::where('activo', true)->orderBy('orden_visualizacion')->get();

        if ($materias->isEmpty()) {
            throw new \Exception('No hay materias activas disponibles para crear el examen.');
        }

        DB::beginTransaction();

        try {
            $examen = Examen::create([
                'user_id' => $user->id,
                'tipo_examen' => $tipo,
                'estado' => EstadoExamen::EnProgreso,
                'fecha_inicio' => Carbon::now(),
            ]);

            foreach ($materias as $materia) {
                $preguntas = Pregunta::query()
                    ->where('materia_id', $materia->id)
                    ->where('activo', true)
                    ->inRandomOrder()
                    ->limit($materia->cantidad_preguntas)
                    ->get();

                $seccionExamen = SeccionExamen::create([
                    'examen_id' => $examen->id,
                    'materia_id' => $materia->id,
                    'total_preguntas' => $preguntas->count(),
                    'respuestas_correctas' => 0,
                    'puntaje' => 0,
                    'tiempo_gastado' => 0,
                ]);

                foreach ($preguntas as $pregunta) {
                    $pregunta->seccionesExamen()->attach($seccionExamen->id);
                }
            }

            $examen->load(['seccionesExamen.materia', 'seccionesExamen.preguntas.opcionesRespuesta']);

            DB::commit();

            return $examen;
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function calcularPuntaje(Examen $examen): void
    {
        DB::beginTransaction();

        try {
            $totalPuntaje = 0;
            $tiempoTotal = 0;

            foreach ($examen->seccionesExamen as $seccion) {
                $respuestasCorrectas = $seccion->respuestasUsuario()
                    ->where('es_correcta', true)
                    ->count();

                $puntajeSeccion = $seccion->total_preguntas > 0
                    ? ($respuestasCorrectas / $seccion->total_preguntas) * 100
                    : 0;

                $seccion->update([
                    'respuestas_correctas' => $respuestasCorrectas,
                    'puntaje' => $puntajeSeccion,
                ]);

                $totalPuntaje += $puntajeSeccion;
                $tiempoTotal += $seccion->tiempo_gastado;
            }

            $puntajeTotal = $examen->seccionesExamen->count() > 0
                ? $totalPuntaje / $examen->seccionesExamen->count()
                : 0;

            $examen->update([
                'puntaje_total' => round($puntajeTotal, 2),
                'tiempo_gastado' => $tiempoTotal,
                'estado' => EstadoExamen::Completado,
                'fecha_completado' => Carbon::now(),
            ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function abandonarExamen(Examen $examen): void
    {
        $examen->update([
            'estado' => EstadoExamen::Abandonado,
        ]);
    }
}
