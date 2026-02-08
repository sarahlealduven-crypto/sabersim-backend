<?php

namespace App\Filament\Resources\Topicos;

use App\Filament\Resources\Topicos\Pages\CreateTopico;
use App\Filament\Resources\Topicos\Pages\EditTopico;
use App\Filament\Resources\Topicos\Pages\ListTopicos;
use App\Filament\Resources\Topicos\RelationManagers\PreguntasRelationManager;
use App\Filament\Resources\Topicos\Schemas\TopicoForm;
use App\Filament\Resources\Topicos\Tables\TopicosTable;
use App\Models\Topico;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class TopicoResource extends Resource
{
    protected static ?string $model = Topico::class;

    protected static ?string $modelLabel = 'Tema';

    protected static ?string $pluralModelLabel = 'Temas';

    protected static string|UnitEnum|null $navigationGroup = 'Banco de Preguntas';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return TopicoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TopicosTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            PreguntasRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTopicos::route('/'),
            'create' => CreateTopico::route('/create'),
            'edit' => EditTopico::route('/{record}/edit'),
        ];
    }
}
