<?php

namespace App\Filament\Resources\MaterialApoyos;

use App\Filament\Resources\MaterialApoyos\Pages\CreateMaterialApoyo;
use App\Filament\Resources\MaterialApoyos\Pages\EditMaterialApoyo;
use App\Filament\Resources\MaterialApoyos\Pages\ListMaterialApoyos;
use App\Filament\Resources\MaterialApoyos\Schemas\MaterialApoyoForm;
use App\Filament\Resources\MaterialApoyos\Tables\MaterialApoyosTable;
use App\Models\MaterialApoyo;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class MaterialApoyoResource extends Resource
{
    protected static ?string $model = MaterialApoyo::class;

    protected static ?string $modelLabel = 'Material de apoyo';

    protected static ?string $pluralModelLabel = 'Materiales de apoyo';

    protected static string|UnitEnum|null $navigationGroup = 'Banco de Preguntas';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPlayCircle;

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return MaterialApoyoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MaterialApoyosTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMaterialApoyos::route('/'),
            'create' => CreateMaterialApoyo::route('/create'),
            'edit' => EditMaterialApoyo::route('/{record}/edit'),
        ];
    }
}
