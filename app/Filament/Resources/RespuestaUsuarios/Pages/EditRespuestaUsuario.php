<?php

namespace App\Filament\Resources\RespuestaUsuarios\Pages;

use App\Filament\Resources\RespuestaUsuarios\RespuestaUsuarioResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRespuestaUsuario extends EditRecord
{
    protected static string $resource = RespuestaUsuarioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
