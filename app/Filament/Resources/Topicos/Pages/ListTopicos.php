<?php

namespace App\Filament\Resources\Topicos\Pages;

use App\Filament\Resources\Topicos\TopicoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTopicos extends ListRecords
{
    protected static string $resource = TopicoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
