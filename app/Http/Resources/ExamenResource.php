<?php

namespace App\Http\Resources;

use Dedoc\Scramble\Attributes\SchemaName;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

#[SchemaName('Examen')]
class ExamenResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            /**
             * Identificador único del examen.
             *
             * @example 1
             */
            'id' => $this->id,

            /**
             * ID del usuario propietario de este examen.
             *
             * @example 1
             */
            'user_id' => $this->user_id,

            /**
             * Tipo de examen: completo o por_materia.
             *
             * @example "completo"
             */
            'tipo_examen' => $this->tipo_examen?->value,

            /**
             * Estado actual: en_progreso, completado o abandonado.
             *
             * @example "en_progreso"
             */
            'estado' => $this->estado?->value,

            /**
             * Marca de tiempo de inicio del examen.
             *
             * @format date-time
             *
             * @example "2024-01-15T10:30:00.000000Z"
             */
            'fecha_inicio' => $this->fecha_inicio,

            /**
             * Marca de tiempo de finalización del examen (null si no está completado).
             *
             * @format date-time
             *
             * @example "2024-01-15T11:00:00.000000Z"
             */
            'fecha_completado' => $this->fecha_completado,

            /**
             * Puntaje total obtenido (0-100).
             *
             * @example 85
             */
            'puntaje_total' => $this->puntaje_total,

            /**
             * Tiempo total gastado en segundos.
             *
             * @example 1800
             */
            'tiempo_gastado' => $this->tiempo_gastado,

            /**
             * Lista de secciones del examen por materia.
             */
            'secciones' => SeccionExamenResource::collection($this->whenLoaded('seccionesExamen')),
        ];
    }
}
