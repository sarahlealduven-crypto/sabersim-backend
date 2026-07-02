<?php

namespace App\Http\Resources;

use Dedoc\Scramble\Attributes\SchemaName;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

#[SchemaName('SeccionExamen')]
class SeccionExamenResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            /**
             * Identificador único de la sección del examen.
             *
             * @example 1
             */
            'id' => $this->id,

            /**
             * ID del examen padre.
             *
             * @example 1
             */
            'examen_id' => $this->examen_id,

            /**
             * Información de la materia para esta sección.
             */
            'materia' => MateriaResource::make($this->whenLoaded('materia')),

            /**
             * Puntaje obtenido en esta sección.
             *
             * @example 85
             */
            'puntaje' => $this->puntaje,

            /**
             * Número de respuestas correctas en esta sección.
             *
             * @example 8
             */
            'respuestas_correctas' => $this->respuestas_correctas,

            /**
             * Número total de preguntas en esta sección.
             *
             * @example 10
             */
            'total_preguntas' => $this->total_preguntas,

            /**
             * Tiempo gastado en segundos en esta sección.
             *
             * @example 600
             */
            'tiempo_gastado' => $this->tiempo_gastado,

            /**
             * Lista de preguntas en esta sección.
             */
            'preguntas' => PreguntaResource::collection($this->whenLoaded('preguntas')),
        ];
    }
}
