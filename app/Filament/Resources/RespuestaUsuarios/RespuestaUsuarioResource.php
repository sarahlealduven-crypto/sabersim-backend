<?php

namespace App\Filament\Resources\RespuestaUsuarios;

use App\Filament\Resources\RespuestaUsuarios\Pages\CreateRespuestaUsuario;
use App\Filament\Resources\RespuestaUsuarios\Pages\EditRespuestaUsuario;
use App\Filament\Resources\RespuestaUsuarios\Pages\ListRespuestaUsuarios;
use App\Filament\Resources\RespuestaUsuarios\Schemas\RespuestaUsuarioForm;
use App\Filament\Resources\RespuestaUsuarios\Tables\RespuestaUsuariosTable;
use App\Models\RespuestaUsuario;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class RespuestaUsuarioResource extends Resource
{
    protected static ?string $model = RespuestaUsuario::class;

    protected static ?string $modelLabel = 'Respuesta de usuario';

    protected static ?string $pluralModelLabel = 'Respuestas de usuario';

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return RespuestaUsuarioForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RespuestaUsuariosTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRespuestaUsuarios::route('/'),
            'create' => CreateRespuestaUsuario::route('/create'),
            'edit' => EditRespuestaUsuario::route('/{record}/edit'),
        ];
    }
}
