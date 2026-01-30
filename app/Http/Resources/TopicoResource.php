<?php

namespace App\Http\Resources;

use Dedoc\Scramble\Attributes\SchemaName;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

#[SchemaName('Topico')]
class TopicoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            /**
             * Identificador único del tema.
             *
             * @example 1
             */
            'id' => $this->id,

            /**
             * ID de la materia padre.
             *
             * @example 1
             */
            'materia_id' => $this->materia_id,

            /**
             * Nombre para mostrar del tema.
             *
             * @example "Ecuaciones Lineales"
             */
            'nombre' => $this->nombre,

            /**
             * Slug amigable.
             *
             * @example "ecuaciones-lineales"
             */
            'slug' => $this->slug,

            /**
             * Descripción del tema.
             *
             * @example "Resolución de ecuaciones de primer grado"
             */
            'descripcion' => $this->descripcion,
        ];
    }
}
