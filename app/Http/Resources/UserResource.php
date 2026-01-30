<?php

namespace App\Http\Resources;

use Dedoc\Scramble\Attributes\SchemaName;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

#[SchemaName('User')]
class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            /**
             * Identificador único del usuario.
             *
             * @example 1
             */
            'id' => $this->id,

            /**
             * Nombre completo del usuario.
             *
             * @example "Juan Pérez"
             */
            'name' => $this->name,

            /**
             * Correo electrónico del usuario.
             *
             * @example "juan@example.com"
             */
            'email' => $this->email,

            /**
             * Nivel de grado del estudiante (6-14).
             *
             * @example 11
             */
            'grade_level' => $this->grade_level,

            /**
             * Nivel actual del usuario basado en el progreso de XP.
             *
             * @example 3
             */
            'current_level' => $this->current_level,

            /**
             * Total de puntos de experiencia ganados.
             *
             * @example 1500
             */
            'total_xp' => $this->total_xp,

            /**
             * Marca de tiempo de creación de la cuenta.
             *
             * @format date-time
             *
             * @example "2024-01-15T10:30:00.000000Z"
             */
            'created_at' => $this->created_at,
        ];
    }
}
