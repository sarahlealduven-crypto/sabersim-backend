<?php

namespace App\Filament\Resources\OpcionRespuestas\Pages;

use App\Filament\Resources\OpcionRespuestas\OpcionRespuestaResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditOpcionRespuesta extends EditRecord
{
    protected static string $resource = OpcionRespuestaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
