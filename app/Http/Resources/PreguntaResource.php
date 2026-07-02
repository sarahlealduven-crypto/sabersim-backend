<?php

namespace App\Http\Resources;

use Dedoc\Scramble\Attributes\SchemaName;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

#[SchemaName('Pregunta')]
class PreguntaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            /**
             * Identificador único de la pregunta.
             *
             * @example 1
             */
            'id' => $this->id,

            /**
             * ID de la materia a la que pertenece la pregunta.
             *
             * @example 1
             */
            'materia_id' => $this->materia_id,

            /**
             * ID del tema dentro de la materia.
             *
             * @example 5
             */
            'topico_id' => $this->topico_id,

            /**
             * El texto de la pregunta.
             *
             * @example "¿Cuál es el resultado de 2 + 2?"
             */
            'texto_pregunta' => $this->texto_pregunta,

            /**
             * Contexto o escenario adicional para la pregunta (opcional).
             *
             * @example "Imagina que tienes 2 manzanas y consigues 2 más..."
             */
            'texto_contexto' => $this->texto_contexto,

            /**
             * Nivel de dificultad: facil, medio o dificil.
             *
             * @example "facil"
             */
            'nivel_dificultad' => $this->nivel_dificultad?->value,

            /**
             * Explicación de la respuesta correcta (se muestra después de enviar).
             *
             * @example "La suma de 2 más 2 es igual a 4"
             */
            'explicacion' => $this->explicacion,

            /**
             * Si la pregunta está activa y disponible para exámenes.
             *
             * @example true
             */
            'activo' => $this->activo,

            /**
             * Lista de opciones de respuesta.
             */
            'opciones' => OpcionRespuestaResource::collection($this->whenLoaded('opcionesRespuesta')),
        ];
    }
}
