<?php

namespace App\Http\Resources;

use Dedoc\Scramble\Attributes\SchemaName;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

#[SchemaName('EstadisticaUsuario')]
class EstadisticaUsuarioResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            /**
             * Identificador único del registro de estadísticas.
             *
             * @example 1
             */
            'id' => $this->id,

            /**
             * ID del usuario al que pertenecen estas estadísticas.
             *
             * @example 1
             */
            'user_id' => $this->user_id,

            /**
             * Información de la materia (null para estadísticas generales).
             */
            'materia' => MateriaResource::make($this->whenLoaded('materia') ? $this->materia : null),

            /**
             * Número total de exámenes realizados.
             *
             * @example 25
             */
            'total_examenes' => $this->total_examenes,

            /**
             * Número total de preguntas respondidas.
             *
             * @example 250
             */
            'total_preguntas_respondidas' => $this->total_preguntas_respondidas,

            /**
             * Número total de respuestas correctas.
             *
             * @example 200
             */
            'respuestas_correctas' => $this->respuestas_correctas,

            /**
             * Porcentaje de puntaje promedio (0-100).
             *
             * @example 80
             */
            'puntaje_promedio' => $this->puntaje_promedio,

            /**
             * Mejor puntaje obtenido (0-100).
             *
             * @example 95
             */
            'mejor_puntaje' => $this->mejor_puntaje,

            /**
             * Tiempo total gastado en segundos.
             *
             * @example 15000
             */
            'tiempo_total_gastado' => $this->tiempo_total_gastado,

            /**
             * Fecha del último examen realizado.
             *
             * @format date-time
             *
             * @example "2024-01-15T11:00:00.000000Z"
             */
            'fecha_ultimo_examen' => $this->fecha_ultimo_examen,
        ];
    }
}
