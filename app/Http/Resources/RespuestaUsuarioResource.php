<?php

namespace App\Http\Resources;

use Dedoc\Scramble\Attributes\SchemaName;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

#[SchemaName('RespuestaUsuario')]
class RespuestaUsuarioResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            /**
             * Identificador único de la respuesta del usuario.
             *
             * @example 1
             */
            'id' => $this->id,

            /**
             * ID del usuario que respondió.
             *
             * @example 1
             */
            'user_id' => $this->user_id,

            /**
             * ID de la sección del examen.
             *
             * @example 5
             */
            'seccion_examen_id' => $this->seccion_examen_id,

            /**
             * ID de la pregunta respondida.
             *
             * @example 10
             */
            'pregunta_id' => $this->pregunta_id,

            /**
             * ID de la opción seleccionada por el usuario.
             *
             * @example 20
             */
            'opcion_seleccionada_id' => $this->opcion_seleccionada_id,

            /**
             * Si la respuesta fue correcta.
             *
             * @example true
             */
            'es_correcta' => $this->es_correcta,

            /**
             * Tiempo gastado en segundos para responder.
             *
             * @example 30
             */
            'tiempo_gastado' => $this->tiempo_gastado,
        ];
    }
}
