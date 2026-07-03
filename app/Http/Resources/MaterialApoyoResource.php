<?php

namespace App\Http\Resources;

use Dedoc\Scramble\Attributes\SchemaName;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

#[SchemaName('MaterialApoyo')]
class MaterialApoyoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'titulo' => $this->titulo,
            'slug' => $this->slug,
            'descripcion' => $this->descripcion,
            'tipo' => $this->tipo,
            'source_url' => $this->source_url,
            'embed_url' => $this->embed_url,
            'thumbnail_url' => $this->thumbnail_url,
            'duracion' => $this->duracion,
            'activo' => $this->activo,
            'orden_visualizacion' => $this->orden_visualizacion,
            'materia' => new MateriaResource($this->whenLoaded('materia')),
        ];
    }
}
