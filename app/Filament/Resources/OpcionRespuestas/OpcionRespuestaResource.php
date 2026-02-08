<?php

namespace App\Filament\Resources\OpcionRespuestas;

use App\Filament\Resources\OpcionRespuestas\Pages\CreateOpcionRespuesta;
use App\Filament\Resources\OpcionRespuestas\Pages\EditOpcionRespuesta;
use App\Filament\Resources\OpcionRespuestas\Pages\ListOpcionRespuestas;
use App\Filament\Resources\OpcionRespuestas\Schemas\OpcionRespuestaForm;
use App\Filament\Resources\OpcionRespuestas\Tables\OpcionRespuestasTable;
use App\Models\OpcionRespuesta;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class OpcionRespuestaResource extends Resource
{
    protected static ?string $model = OpcionRespuesta::class;

    protected static ?string $modelLabel = 'Opción de respuesta';

    protected static ?string $pluralModelLabel = 'Opciones de respuesta';

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return OpcionRespuestaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OpcionRespuestasTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOpcionRespuestas::route('/'),
            'create' => CreateOpcionRespuesta::route('/create'),
            'edit' => EditOpcionRespuesta::route('/{record}/edit'),
        ];
    }
}
