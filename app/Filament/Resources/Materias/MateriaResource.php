<?php

namespace App\Filament\Resources\Materias;

use App\Filament\Resources\Materias\Pages\CreateMateria;
use App\Filament\Resources\Materias\Pages\EditMateria;
use App\Filament\Resources\Materias\Pages\ListMaterias;
use App\Filament\Resources\Materias\RelationManagers\PreguntasRelationManager;
use App\Filament\Resources\Materias\RelationManagers\TopicosRelationManager;
use App\Filament\Resources\Materias\Schemas\MateriaForm;
use App\Filament\Resources\Materias\Tables\MateriasTable;
use App\Models\Materia;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class MateriaResource extends Resource
{
    protected static ?string $model = Materia::class;

    protected static ?string $modelLabel = 'Materia';

    protected static ?string $pluralModelLabel = 'Materias';

    protected static string|UnitEnum|null $navigationGroup = 'Banco de Preguntas';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return MateriaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MateriasTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            TopicosRelationManager::class,
            PreguntasRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMaterias::route('/'),
            'create' => CreateMateria::route('/create'),
            'edit' => EditMateria::route('/{record}/edit'),
        ];
    }
}
