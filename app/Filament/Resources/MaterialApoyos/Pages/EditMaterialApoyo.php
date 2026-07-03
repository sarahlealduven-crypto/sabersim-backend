<?php

namespace App\Filament\Resources\MaterialApoyos\Pages;

use App\Filament\Resources\MaterialApoyos\MaterialApoyoResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMaterialApoyo extends EditRecord
{
    protected static string $resource = MaterialApoyoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
