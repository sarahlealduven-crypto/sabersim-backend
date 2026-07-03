<?php

namespace App\Filament\Resources\Examens\Infolists;

use App\Enums\EstadoExamen;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ExamenInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Información del examen')
                    ->description('Detalles de este examen')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('user.name')
                                    ->label('Estudiante')
                                    ->icon('heroicon-o-user'),

                                TextEntry::make('tipo_examen')
                                    ->label('Tipo')
                                    ->badge()
                                    ->icon('heroicon-o-document-text'),

                                TextEntry::make('estado')
                                    ->label('Estado')
                                    ->badge()
                                    ->color(fn (EstadoExamen $state): string => self::estadoColor($state))
                                    ->icon('heroicon-o-circle-stack'),

                                TextEntry::make('puntaje_total')
                                    ->label('Puntaje total')
                                    ->formatStateUsing(fn ($state): string => $state.'%')
                                    ->icon('heroicon-o-chart-bar')
                                    ->badge()
                                    ->color(fn ($state): ?string => $state >= 70 ? 'success' : ($state >= 50 ? 'warning' : 'danger')),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextEntry::make('fecha_inicio')
                                    ->label('Iniciado el')
                                    ->dateTime()
                                    ->icon('heroicon-o-clock'),

                                TextEntry::make('fecha_completado')
                                    ->label('Completado el')
                                    ->dateTime()
                                    ->icon('heroicon-o-check-circle'),
                            ]),

                        TextEntry::make('tiempo_gastado')
                            ->label('Tiempo empleado')
                            ->formatStateUsing(fn ($state): string => $state ? self::formatTime($state) : '00:00')
                            ->icon('heroicon-o-clock'),
                    ]),

                Section::make('Resumen de secciones')
                    ->description('Resumen de las secciones del examen')
                    ->schema([
                        TextEntry::make('secciones_count')
                            ->label('Total de secciones')
                            ->getStateUsing(fn ($record): int => $record->seccionesExamen()->count())
                            ->numeric()
                            ->icon('heroicon-squares-2x2'),

                        TextEntry::make('total_preguntas')
                            ->label('Total de preguntas')
                            ->getStateUsing(fn ($record): int => (int) $record->seccionesExamen()->sum('total_preguntas'))
                            ->numeric()
                            ->icon('heroicon-o-question-mark-circle'),

                        TextEntry::make('total_correctas')
                            ->label('Respuestas correctas')
                            ->getStateUsing(fn ($record): int => (int) $record->seccionesExamen()->sum('respuestas_correctas'))
                            ->numeric()
                            ->icon('heroicon-o-check-circle'),

                        TextEntry::make('created_at')
                            ->label('Creado el')
                            ->dateTime(),

                        TextEntry::make('updated_at')
                            ->label('Actualizado el')
                            ->dateTime(),
                    ]),
            ]);
    }

    protected static function formatTime(?int $seconds): string
    {
        if ($seconds === null || $seconds === 0) {
            return '00:00:00';
        }

        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
    }

    protected static function estadoColor(EstadoExamen $state): string
    {
        return match ($state) {
            EstadoExamen::EnProgreso => 'info',
            EstadoExamen::Completado => 'success',
            EstadoExamen::Abandonado => 'danger',
        };
    }
}
