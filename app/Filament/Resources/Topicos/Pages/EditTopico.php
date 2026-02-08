<?php

namespace App\Filament\Resources\Topicos\Pages;

use App\Filament\Resources\Topicos\TopicoResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTopico extends EditRecord
{
    protected static string $resource = TopicoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
