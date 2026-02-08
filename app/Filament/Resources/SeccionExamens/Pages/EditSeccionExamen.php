<?php

namespace App\Filament\Resources\SeccionExamens\Pages;

use App\Filament\Resources\SeccionExamens\SeccionExamenResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSeccionExamen extends EditRecord
{
    protected static string $resource = SeccionExamenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
