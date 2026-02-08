<?php

namespace App\Filament\Resources\EstadisticaUsuarios\Pages;

use App\Filament\Resources\EstadisticaUsuarios\EstadisticaUsuarioResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditEstadisticaUsuario extends EditRecord
{
    protected static string $resource = EstadisticaUsuarioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
