<?php

namespace App\Filament\Resources\EstadisticaUsuarios;

use App\Filament\Resources\EstadisticaUsuarios\Infolists\EstadisticaUsuarioInfolist;
use App\Filament\Resources\EstadisticaUsuarios\Pages\CreateEstadisticaUsuario;
use App\Filament\Resources\EstadisticaUsuarios\Pages\EditEstadisticaUsuario;
use App\Filament\Resources\EstadisticaUsuarios\Pages\ListEstadisticaUsuarios;
use App\Filament\Resources\EstadisticaUsuarios\Schemas\EstadisticaUsuarioForm;
use App\Filament\Resources\EstadisticaUsuarios\Tables\EstadisticaUsuariosTable;
use App\Models\EstadisticaUsuario;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class EstadisticaUsuarioResource extends Resource
{
    protected static ?string $model = EstadisticaUsuario::class;

    protected static ?string $modelLabel = 'Estadística de usuario';

    protected static ?string $pluralModelLabel = 'Estadísticas de usuario';

    protected static string|UnitEnum|null $navigationGroup = 'Estadísticas';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return EstadisticaUsuarioForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return EstadisticaUsuarioInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EstadisticaUsuariosTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEstadisticaUsuarios::route('/'),
            'create' => CreateEstadisticaUsuario::route('/create'),
            'edit' => EditEstadisticaUsuario::route('/{record}/edit'),
        ];
    }
}
