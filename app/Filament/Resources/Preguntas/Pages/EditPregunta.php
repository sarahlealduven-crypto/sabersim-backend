<?php

namespace App\Filament\Resources\Preguntas\Pages;

use App\Filament\Resources\Preguntas\PreguntaResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\Width;

class EditPregunta extends EditRecord
{
    protected static string $resource = PreguntaResource::class;

    public function getMaxContentWidth(): Width
    {
        return Width::Full;
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
