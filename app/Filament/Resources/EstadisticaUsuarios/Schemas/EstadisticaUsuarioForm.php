<?php

namespace App\Filament\Resources\EstadisticaUsuarios\Schemas;

use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EstadisticaUsuarioForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Resumen de estadísticas')
                    ->description('Estadísticas calculadas del usuario (solo lectura)')
                    ->schema([
                        Select::make('user_id')
                            ->label('Estudiante')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->disabled(),

                        Select::make('materia_id')
                            ->label('Materia')
                            ->relationship('materia', 'nombre')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->disabled(),

                        TextInput::make('total_examenes')
                            ->label('Total de exámenes')
                            ->numeric()
                            ->minValue(0)
                            ->disabled(),

                        TextInput::make('total_preguntas')
                            ->label('Total de preguntas')
                            ->numeric()
                            ->minValue(0)
                            ->disabled(),

                        TextInput::make('total_correctas')
                            ->label('Total correctas')
                            ->numeric()
                            ->minValue(0)
                            ->disabled(),

                        TextInput::make('porcentaje')
                            ->label('Puntaje global (%)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->step(0.01)
                            ->suffix('%')
                            ->disabled(),
                    ])
                    ->columns(2),

                Section::make('Métricas de rendimiento')
                    ->description('Indicadores de rendimiento adicionales')
                    ->schema([
                        TextInput::make('puntaje_promedio')
                            ->label('Puntaje promedio (%)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->step(0.01)
                            ->suffix('%')
                            ->disabled(),

                        TextInput::make('mejor_puntaje')
                            ->label('Mejor puntaje (%)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->step(0.01)
                            ->suffix('%')
                            ->disabled(),

                        TextInput::make('tiempo_total')
                            ->label('Tiempo total empleado (segundos)')
                            ->numeric()
                            ->minValue(0)
                            ->disabled()
                            ->formatStateUsing(fn($state): string => $state ? self::formatTime($state) : '00:00'),
                    ])
                    ->columns(2),
            ]);
    }

    protected static function formatTime(?int $seconds): string
    {
        if ($seconds === null || $seconds === 0) {
            return '00:00';
        }

        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;

        if ($hours > 0) {
            return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
        }
        return sprintf('%02d:%02d', $minutes, $secs);
    }
}
