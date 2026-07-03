<?php

namespace App\Filament\Resources\MaterialApoyos\Pages;

use App\Filament\Resources\MaterialApoyos\MaterialApoyoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMaterialApoyos extends ListRecords
{
    protected static string $resource = MaterialApoyoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
