<?php

namespace App\Filament\Resources\Preguntas;

use App\Filament\Resources\Preguntas\Pages\CreatePregunta;
use App\Filament\Resources\Preguntas\Pages\EditPregunta;
use App\Filament\Resources\Preguntas\Pages\ListPreguntas;
use App\Filament\Resources\Preguntas\RelationManagers\OpcionesRespuestaRelationManager;
use App\Filament\Resources\Preguntas\Schemas\PreguntaForm;
use App\Filament\Resources\Preguntas\Tables\PreguntasTable;
use App\Models\Pregunta;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class PreguntaResource extends Resource
{
    protected static ?string $model = Pregunta::class;

    protected static ?string $modelLabel = 'Pregunta';

    protected static ?string $pluralModelLabel = 'Preguntas';

    protected static string|UnitEnum|null $navigationGroup = 'Banco de Preguntas';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQuestionMarkCircle;

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return PreguntaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PreguntasTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            OpcionesRespuestaRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPreguntas::route('/'),
            'create' => CreatePregunta::route('/create'),
            'edit' => EditPregunta::route('/{record}/edit'),
        ];
    }
}
