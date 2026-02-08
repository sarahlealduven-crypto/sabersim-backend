<?php

namespace App\Filament\Resources\OpcionRespuestas\Schemas;

use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class OpcionRespuestaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detalles de la opción de respuesta')
                    ->description('Opción de respuesta tipo test')
                    ->schema([
                        Select::make('pregunta_id')
                            ->label('Pregunta')
                            ->relationship('pregunta', 'texto_pregunta')
                            ->searchable()
                            ->preload()
                            ->helperText('Pregunta a la que pertenece esta respuesta')
                            ->required(),

                        Select::make('letra_opcion')
                            ->label('Letra de opción')
                            ->options([
                                'A' => 'A',
                                'B' => 'B',
                                'C' => 'C',
                                'D' => 'D',
                                'E' => 'E',
                                'F' => 'F',
                            ])
                            ->required()
                            ->helperText('Una letra de la A a la F'),

                        Toggle::make('es_correcta')
                            ->label('Respuesta correcta')
                            ->inline(false)
                            ->helperText('Marcar si esta es la respuesta correcta'),
                    ])
                    ->columns(2),

                Section::make('Contenido de la opción')
                    ->schema([
                        Textarea::make('texto_opcion')
                            ->label('Texto de la opción')
                            ->required()
                            ->rows(3)
                            ->columnSpanFull()
                            ->autofocus(),
                    ]),
            ]);
    }
}
