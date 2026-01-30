<?php

namespace App\Http\Resources;

use Dedoc\Scramble\Attributes\SchemaName;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

#[SchemaName('OpcionRespuesta')]
class OpcionRespuestaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            /**
             * Identificador único de la opción de respuesta.
             *
             * @example 1
             */
            'id' => $this->id,

            /**
             * ID de la pregunta padre.
             *
             * @example 5
             */
            'pregunta_id' => $this->pregunta_id,

            /**
             * Letra de la opción (A, B, C, D, etc.).
             *
             * @example "A"
             */
            'letra_opcion' => $this->letra_opcion,

            /**
             * Contenido de texto de la opción.
             *
             * @example "Es la respuesta correcta"
             */
            'texto_opcion' => $this->texto_opcion,
        ];
    }
}
