<?php

namespace App\Filament\Resources\Topicos\Pages;

use App\Filament\Resources\Topicos\TopicoResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTopico extends CreateRecord
{
    protected static string $resource = TopicoResource::class;
}
