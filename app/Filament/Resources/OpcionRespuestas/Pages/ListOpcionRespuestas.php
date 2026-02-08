<?php

namespace App\Filament\Resources\OpcionRespuestas\Pages;

use App\Filament\Resources\OpcionRespuestas\OpcionRespuestaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListOpcionRespuestas extends ListRecords
{
    protected static string $resource = OpcionRespuestaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
