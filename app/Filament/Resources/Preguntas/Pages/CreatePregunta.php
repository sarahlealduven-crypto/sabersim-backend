<?php

namespace App\Filament\Resources\Preguntas\Pages;

use App\Filament\Resources\Preguntas\PreguntaResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;

class CreatePregunta extends CreateRecord
{
    protected static string $resource = PreguntaResource::class;

    public function getMaxContentWidth(): Width
    {
        return Width::Full;
    }
}
