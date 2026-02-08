<?php

namespace App\Filament\Resources\Examens;

use App\Filament\Resources\Examens\Infolists\ExamenInfolist;
use App\Filament\Resources\Examens\Pages\CreateExamen;
use App\Filament\Resources\Examens\Pages\EditExamen;
use App\Filament\Resources\Examens\Pages\ListExamens;
use App\Filament\Resources\Examens\RelationManagers\SeccionesExamenRelationManager;
use App\Filament\Resources\Examens\Schemas\ExamenForm;
use App\Filament\Resources\Examens\Tables\ExamensTable;
use App\Models\Examen;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ExamenResource extends Resource
{
    protected static ?string $model = Examen::class;

    protected static ?string $modelLabel = 'Examen';

    protected static ?string $pluralModelLabel = 'Exámenes';

    protected static string|UnitEnum|null $navigationGroup = 'Gestión de Exámenes';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return ExamenForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ExamenInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ExamensTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            SeccionesExamenRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListExamens::route('/'),
            'create' => CreateExamen::route('/create'),
            'edit' => EditExamen::route('/{record}/edit'),
        ];
    }
}
