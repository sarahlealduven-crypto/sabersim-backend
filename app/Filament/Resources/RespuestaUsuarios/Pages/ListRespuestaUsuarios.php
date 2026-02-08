<?php

namespace App\Filament\Resources\RespuestaUsuarios\Pages;

use App\Filament\Resources\RespuestaUsuarios\RespuestaUsuarioResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRespuestaUsuarios extends ListRecords
{
    protected static string $resource = RespuestaUsuarioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
