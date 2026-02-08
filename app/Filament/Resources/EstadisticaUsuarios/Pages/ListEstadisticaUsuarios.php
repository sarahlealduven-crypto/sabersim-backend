<?php

namespace App\Filament\Resources\EstadisticaUsuarios\Pages;

use App\Filament\Resources\EstadisticaUsuarios\EstadisticaUsuarioResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEstadisticaUsuarios extends ListRecords
{
    protected static string $resource = EstadisticaUsuarioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
