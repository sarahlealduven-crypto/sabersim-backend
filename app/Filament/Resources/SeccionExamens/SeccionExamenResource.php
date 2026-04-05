<?php

namespace App\Filament\Resources\SeccionExamens;

use App\Filament\Resources\SeccionExamens\Pages\CreateSeccionExamen;
use App\Filament\Resources\SeccionExamens\Pages\EditSeccionExamen;
use App\Filament\Resources\SeccionExamens\Pages\ListSeccionExamens;
use App\Filament\Resources\SeccionExamens\RelationManagers\PreguntasRelationManager;
use App\Filament\Resources\SeccionExamens\RelationManagers\RespuestasUsuarioRelationManager;
use App\Filament\Resources\SeccionExamens\Schemas\SeccionExamenForm;
use App\Filament\Resources\SeccionExamens\Tables\SeccionExamensTable;
use App\Models\SeccionExamen;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class SeccionExamenResource extends Resource
{
    protected static ?string $model = SeccionExamen::class;

    protected static ?string $modelLabel = 'Sección de examen';

    protected static ?string $pluralModelLabel = 'Secciones de examen';

    protected static string|UnitEnum|null $navigationGroup = 'Gestión de Exámenes';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return SeccionExamenForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SeccionExamensTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            'preguntas' => PreguntasRelationManager::class,
            'respuestasUsuario' => RespuestasUsuarioRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSeccionExamens::route('/'),
            'create' => CreateSeccionExamen::route('/create'),
            'edit' => EditSeccionExamen::route('/{record}/edit'),
        ];
    }
}
