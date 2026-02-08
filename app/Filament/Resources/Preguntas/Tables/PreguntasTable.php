<?php

namespace App\Filament\Resources\Preguntas\Tables;

use App\Enums\NivelDificultad;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class PreguntasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('texto_pregunta')
                    ->label('Pregunta')
                    ->searchable()
                    ->limit(100)
                    ->wrap()
                    ->description(fn($record): string => $record->topico->materia->nombre ?? ''),

                TextColumn::make('materia.nombre')
                    ->label('Materia')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),

                TextColumn::make('topico.nombre')
                    ->label('Tema')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('warning'),

                TextColumn::make('nivel_dificultad')
                    ->label('Dificultad')
                    ->badge()
                    ->color(fn(NivelDificultad $state): string => match ($state) {
                        NivelDificultad::Facil => 'success',
                        NivelDificultad::Medio => 'warning',
                        NivelDificultad::Dificil => 'danger',
                    })
                    ->sortable(),

                TextColumn::make('opciones_count')
                    ->label('Opciones')
                    ->numeric()
                    ->sortable()
                    ->counts('opcionesRespuesta')
                    ->badge()
                    ->color('info'),

                IconColumn::make('activo')
                    ->label('Activa')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('materia')
                    ->label('Materia')
                    ->relationship('materia', 'nombre')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('topico')
                    ->label('Tema')
                    ->relationship('topico', 'nombre')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('nivel_dificultad')
                    ->label('Nivel de dificultad')
                    ->options(NivelDificultad::class),

                TernaryFilter::make('activo')
                    ->label('Activa')
                    ->placeholder('Todas las preguntas')
                    ->trueLabel('Solo activas')
                    ->falseLabel('Solo inactivas')
                    ->default(true),
            ], layout: FiltersLayout::AboveContent)
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('activate')
                        ->label('Marcar como activas')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation(false)
                        ->action(fn($records) => $records->each->update(['activo' => true])),
                    BulkAction::make('deactivate')
                        ->label('Marcar como inactivas')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation(false)
                        ->action(fn($records) => $records->each->update(['activo' => false])),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
