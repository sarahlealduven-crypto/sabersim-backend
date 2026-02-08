<?php

namespace App\Filament\Resources\RespuestaUsuarios\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class RespuestaUsuariosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('seccionExamen.examen.user.name')
                    ->label('Estudiante')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                TextColumn::make('seccionExamen.materia.nombre')
                    ->label('Subject')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),

                TextColumn::make('pregunta.texto_pregunta')
                    ->label('Pregunta')
                    ->searchable()
                    ->limit(50)
                    ->wrap(),

                TextColumn::make('opcionSeleccionada.letra_opcion')
                    ->label('Selected Answer')
                    ->badge()
                    ->color('info')
                    ->description(fn($record): ?string => $record->opcionSeleccionada->texto_opcion ?? '')
                    ->sortable(),

                IconColumn::make('es_correcta')
                    ->label('Correcta')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),

                TextColumn::make('tiempo_gastado')
                    ->label('Time Spent')
                    ->numeric()
                    ->formatStateUsing(fn($state): string => $state ? self::formatTime($state) : '00:00')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Respondido el')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                TernaryFilter::make('es_correcta')
                    ->label('Correcta')
                    ->placeholder('Todas las respuestas')
                    ->trueLabel('Solo correctas')
                    ->falseLabel('Solo incorrectas'),
            ])
            ->recordActions([
                ViewAction::make(),
                DeleteAction::make(),
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
