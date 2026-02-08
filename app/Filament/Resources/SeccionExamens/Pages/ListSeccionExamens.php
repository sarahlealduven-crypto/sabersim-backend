<?php

namespace App\Filament\Resources\SeccionExamens\Pages;

use App\Filament\Resources\SeccionExamens\SeccionExamenResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSeccionExamens extends ListRecords
{
    protected static string $resource = SeccionExamenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
