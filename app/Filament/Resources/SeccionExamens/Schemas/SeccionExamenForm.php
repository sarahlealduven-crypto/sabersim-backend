<?php

namespace App\Filament\Resources\SeccionExamens\Schemas;

use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SeccionExamenForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información de la sección')
                    ->description('Datos de la sección del examen')
                    ->schema([
                        Select::make('materia_id')
                            ->label('Materia')
                            ->relationship('materia', 'nombre')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->disabled(),

                        TextInput::make('total_preguntas')
                            ->label('Total de preguntas')
                            ->numeric()
                            ->minValue(1)
                            ->required()
                            ->disabled(),

                        TextInput::make('respuestas_correctas')
                            ->label('Respuestas correctas')
                            ->numeric()
                            ->minValue(0)
                            ->required()
                            ->disabled(),

                        TextInput::make('puntaje')
                            ->label('Puntaje (%)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->step(0.01)
                            ->suffix('%')
                            ->required()
                            ->disabled(),

                        TextInput::make('tiempo_gastado')
                            ->label('Tiempo empleado (segundos)')
                            ->numeric()
                            ->minValue(0)
                            ->required()
                            ->disabled(),
                    ])
                    ->columns(2),
            ]);
    }
}
