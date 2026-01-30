<?php

namespace App\Services;

use App\Models\EstadisticaUsuario;
use App\Models\Examen;
use App\Models\Materia;
use App\Models\User;

class EstadisticaService
{
    public function actualizarEstadisticas(User $user): void
    {
        $examenes = Examen::where('user_id', $user->id)
            ->where('estado', 'completado')
            ->get();

        $this->actualizarEstadisticasGenerales($user, $examenes);

        $materias = Materia::where('activo', true)->get();

        foreach ($materias as $materia) {
            $this->actualizarEstadisticasPorMateria($user, $materia, $examenes);
        }
    }

    private function actualizarEstadisticasGenerales(User $user, $examenes): void
    {
        $estadistica = EstadisticaUsuario::firstOrCreate([
            'user_id' => $user->id,
            'materia_id' => null,
        ]);

        $totalExamenes = $examenes->count();
        $totalPreguntas = 0;
        $respuestasCorrectas = 0;
        $tiempoTotal = 0;

        foreach ($examenes as $examen) {
            foreach ($examen->seccionesExamen as $seccion) {
                $totalPreguntas += $seccion->total_preguntas;
                $respuestasCorrectas += $seccion->respuestas_correctas;
                $tiempoTotal += $seccion->tiempo_gastado;
            }
        }

        $puntajePromedio = $totalExamenes > 0 ? ($examenes->sum('puntaje_total') / $totalExamenes) : 0;
        $mejorPuntaje = $examenes->max('puntaje_total') ?? 0;

        $estadistica->update([
            'total_examenes' => $totalExamenes,
            'total_preguntas_respondidas' => $totalPreguntas,
            'respuestas_correctas' => $respuestasCorrectas,
            'puntaje_promedio' => round($puntajePromedio, 2),
            'mejor_puntaje' => round($mejorPuntaje, 2),
            'tiempo_total_gastado' => $tiempoTotal,
            'fecha_ultimo_examen' => $examenes->max('fecha_completado'),
        ]);
    }

    private function actualizarEstadisticasPorMateria(User $user, Materia $materia, $examenes): void
    {
        $examenesMateria = $examenes->filter(function ($examen) use ($materia) {
            return $examen->seccionesExamen->contains('materia_id', $materia->id);
        });

        if ($examenesMateria->isEmpty()) {
            return;
        }

        $estadistica = EstadisticaUsuario::firstOrCreate([
            'user_id' => $user->id,
            'materia_id' => $materia->id,
        ]);

        $totalPreguntas = 0;
        $respuestasCorrectas = 0;
        $tiempoTotal = 0;

        foreach ($examenesMateria as $examen) {
            $seccion = $examen->seccionesExamen->firstWhere('materia_id', $materia->id);
            if ($seccion) {
                $totalPreguntas += $seccion->total_preguntas;
                $respuestasCorrectas += $seccion->respuestas_correctas;
                $tiempoTotal += $seccion->tiempo_gastado;
            }
        }

        $puntajePromedio = $estadistica->total_examenes > 0
            ? ($examenesMateria->sum('puntaje_total') / $examenesMateria->count())
            : 0;
        $mejorPuntaje = $examenesMateria->max('puntaje_total') ?? 0;

        $estadistica->update([
            'total_examenes' => $examenesMateria->count(),
            'total_preguntas_respondidas' => $totalPreguntas,
            'respuestas_correctas' => $respuestasCorrectas,
            'puntaje_promedio' => round($puntajePromedio, 2),
            'mejor_puntaje' => round($mejorPuntaje, 2),
            'tiempo_total_gastado' => $tiempoTotal,
            'fecha_ultimo_examen' => $examenesMateria->max('fecha_completado'),
        ]);
    }
}
