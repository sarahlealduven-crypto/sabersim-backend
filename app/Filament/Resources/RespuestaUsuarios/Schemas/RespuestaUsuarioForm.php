<?php

namespace App\Filament\Resources\RespuestaUsuarios\Schemas;

use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class RespuestaUsuarioForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información de la respuesta')
                    ->schema([
                        Select::make('seccion_examen_id')
                            ->label('Sección del examen')
                            ->relationship('seccionExamen', 'materia.nombre')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->disabled(),

                        Select::make('pregunta_id')
                            ->label('Pregunta')
                            ->relationship('pregunta', 'texto_pregunta')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->disabled(),

                        Select::make('opcion_seleccionada_id')
                            ->label('Respuesta seleccionada')
                            ->relationship('opcionSeleccionada', 'texto_opcion')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->disabled(),

                        Toggle::make('es_correcta')
                            ->label('Es correcta')
                            ->disabled()
                            ->inline(false),

                        TextInput::make('tiempo_gastado')
                            ->label('Tiempo empleado (segundos)')
                            ->numeric()
                            ->minValue(0)
                            ->disabled(),
                    ])
                    ->columns(2),
            ]);
    }
}
