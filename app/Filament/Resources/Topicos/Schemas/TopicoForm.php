<?php

namespace App\Filament\Resources\Topicos\Schemas;

use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class TopicoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información del tema')
                    ->description('Materia y datos del tema')
                    ->schema([
                        Select::make('materia_id')
                            ->label('Materia')
                            ->relationship('materia', 'nombre')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('nombre')
                                    ->label('Nombre de la materia')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn($state, callable $set) => $set('slug', \Illuminate\Support\Str::slug($state))),
                                Forms\Components\TextInput::make('slug')
                                    ->label('Slug')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true),
                                Forms\Components\Textarea::make('descripcion')
                                    ->label('Descripción')
                                    ->rows(2)
                                    ->columnSpanFull(),
                            ])
                            ->required(),

                        TextInput::make('nombre')
                            ->label('Nombre del tema')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->autofocus(),

                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText('Se generará automáticamente a partir del nombre'),
                    ])
                    ->columns(2),

                Section::make('Descripción')
                    ->description('Detalles adicionales del tema')
                    ->schema([
                        Textarea::make('descripcion')
                            ->label('Descripción')
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
