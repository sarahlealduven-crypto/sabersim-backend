<?php

namespace App\Filament\Resources\EstadisticaUsuarios\Infolists;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EstadisticaUsuarioInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Información del usuario')
                    ->schema([
                        TextEntry::make('user.name')
                            ->label('Estudiante')
                            ->icon('heroicon-o-user'),

                        TextEntry::make('user.email')
                            ->label('Correo electrónico')
                            ->icon('heroicon-o-envelope'),

                        TextEntry::make('materia.nombre')
                            ->label('Materia')
                            ->badge()
                            ->icon('heroicon-o-book-open'),
                    ]),

                Section::make('Resumen de rendimiento')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('total_examenes')
                                    ->label('Total de exámenes')
                                    ->numeric()
                                    ->badge()
                                    ->color('primary')
                                    ->icon('heroicon-o-document-text'),

                                TextEntry::make('total_preguntas_respondidas')
                                    ->label('Total de preguntas')
                                    ->numeric()
                                    ->badge()
                                    ->color('info')
                                    ->icon('heroicon-o-question-mark-circle'),

                                TextEntry::make('respuestas_correctas')
                                    ->label('Total correctas')
                                    ->numeric()
                                    ->badge()
                                    ->color('success')
                                    ->icon('heroicon-o-check-circle'),
                            ]),
                    ]),

                Section::make('Métricas detalladas')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('puntaje_promedio')
                                    ->label('Puntaje promedio')
                                    ->formatStateUsing(fn ($state): string => number_format($state, 2).'%')
                                    ->badge()
                                    ->color(fn ($state): string => $state >= 70 ? 'success' : ($state >= 50 ? 'warning' : 'danger'))
                                    ->icon('heroicon-o-chart-bar'),

                                TextEntry::make('mejor_puntaje')
                                    ->label('Mejor puntaje')
                                    ->formatStateUsing(fn ($state): string => number_format($state, 2).'%')
                                    ->badge()
                                    ->color('success')
                                    ->icon('heroicon-o-star'),

                                TextEntry::make('tiempo_total_gastado')
                                    ->label('Tiempo total empleado')
                                    ->formatStateUsing(fn ($state): string => $state ? self::formatTime($state) : '00:00')
                                    ->icon('heroicon-o-clock'),

                                TextEntry::make('fecha_ultimo_examen')
                                    ->label('Último examen')
                                    ->dateTime()
                                    ->icon('heroicon-o-calendar'),
                            ]),
                    ]),
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
