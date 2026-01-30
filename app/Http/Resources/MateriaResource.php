<?php

namespace App\Http\Resources;

use Dedoc\Scramble\Attributes\SchemaName;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

#[SchemaName('Materia')]
class MateriaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            /**
             * Identificador único de la materia.
             *
             * @example 1
             */
            'id' => $this->id,

            /**
             * Nombre para mostrar de la materia.
             *
             * @example "Matemáticas"
             */
            'nombre' => $this->nombre,

            /**
             * Slug amigable para URL.
             *
             * @example "matematicas"
             */
            'slug' => $this->slug,

            /**
             * Descripción detallada de la materia.
             *
             * @example "Álgebra, geometría y cálculo básico"
             */
            'descripcion' => $this->descripcion,

            /**
             * Identificador de icono para mostrar en la interfaz.
             *
             * @example "calculator"
             */
            'icono' => $this->icono,

            /**
             * Número total de preguntas disponibles.
             *
             * @example 150
             */
            'cantidad_preguntas' => $this->cantidad_preguntas,

            /**
             * Límite de tiempo en minutos para exámenes de esta materia.
             *
             * @example 60
             */
            'tiempo_limite' => $this->tiempo_limite,

            /**
             * Si la materia está actualmente activa.
             *
             * @example true
             */
            'activo' => $this->activo,

            /**
             * Orden de visualización para ordenar materias en la interfaz.
             *
             * @example 1
             */
            'orden_visualizacion' => $this->orden_visualizacion,

            /**
             * Temas asociados con esta materia.
             */
            'topicos' => TopicoResource::collection($this->whenLoaded('topicos') ? $this->topicos : []),
        ];
    }
}
