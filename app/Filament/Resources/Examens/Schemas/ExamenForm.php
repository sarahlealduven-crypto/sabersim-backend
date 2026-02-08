<?php

namespace App\Filament\Resources\Examens\Schemas;

use App\Enums\EstadoExamen;
use App\Enums\TipoExamen;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Schema;

class ExamenForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información del examen')
                    ->description('Datos básicos del examen')
                    ->schema([
                        Select::make('user_id')
                            ->label('Estudiante')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->disabled(),

                        Select::make('tipo_examen')
                            ->label('Tipo de examen')
                            ->options(TipoExamen::class)
                            ->required()
                            ->disabled(),

                        Select::make('estado')
                            ->label('Estado')
                            ->options(EstadoExamen::class)
                            ->required()
                            ->disabled(),
                    ])
                    ->columns(2),

                Section::make('Fechas y horarios')
                    ->description('Inicio y fin del examen')
                    ->schema([
                        DateTimePicker::make('fecha_inicio')
                            ->label('Iniciado el')
                            ->seconds(false)
                            ->disabled(),

                        DateTimePicker::make('fecha_completado')
                            ->label('Completado el')
                            ->seconds(false)
                            ->disabled(),
                    ])
                    ->columns(2),

                Section::make('Resultados')
                    ->description('Métricas de rendimiento del examen')
                    ->schema([
                        TextInput::make('puntaje_total')
                            ->label('Puntaje total (%)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->step(0.01)
                            ->disabled()
                            ->suffix('%'),

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
