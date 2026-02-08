<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TutorResponseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = is_array($this->resource) ? $this->resource : $this->resource->toArray();

        return [
            'conversation_id' => $data['conversation_id'] ?? null,
            'response' => $data['response'] ?? '',
            'materia' => $data['materia'] ?? null,
            'topico' => $data['topico'] ?? null,
            'created_at' => $data['created_at'] ?? null,
        ];
    }
}
