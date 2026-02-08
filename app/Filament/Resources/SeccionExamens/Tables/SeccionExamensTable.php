<?php

namespace App\Filament\Resources\SeccionExamens\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SeccionExamensTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('examen.user.name')
                    ->label('Estudiante')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                TextColumn::make('materia.nombre')
                    ->label('Materia')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),

                TextColumn::make('total_preguntas')
                    ->label('Preguntas')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('respuestas_correctas')
                    ->label('Correct')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('success'),

                TextColumn::make('puntaje')
                    ->label('Puntaje')
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn($state): string => number_format($state, 2) . '%')
                    ->badge()
                    ->color(fn($state): string => $state >= 70 ? 'success' : ($state >= 50 ? 'warning' : 'danger')),

                TextColumn::make('tiempo_gastado')
                    ->label('Tiempo empleado')
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn($state): string => $state ? self::formatTime($state) : '00:00')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'asc')
            ->filters([
                SelectFilter::make('materia')
                    ->label('Materia')
                    ->relationship('materia', 'nombre')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    protected static function formatTime(?int $seconds): string
    {
        if ($seconds === null || $seconds === 0) {
            return '00:00';
        }

        $minutes = floor($seconds / 60);
        $secs = $seconds % 60;

        return sprintf('%02d:%02d', $minutes, $secs);
    }
}
