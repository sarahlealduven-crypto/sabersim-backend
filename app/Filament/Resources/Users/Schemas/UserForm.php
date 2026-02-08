<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nombre')
                    ->required(),
                TextInput::make('email')
                    ->label('Correo electrónico')
                    ->email()
                    ->required(),
                TextInput::make('grade_level')
                    ->label('Grado')
                    ->numeric(),
                DateTimePicker::make('email_verified_at')
                    ->label('Correo verificado el'),
                TextInput::make('current_level')
                    ->label('Nivel actual')
                    ->required()
                    ->numeric()
                    ->default(1),
                TextInput::make('total_xp')
                    ->label('XP total')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('password')
                    ->label('Contraseña')
                    ->password()
                    ->required(),
            ]);
    }
}
