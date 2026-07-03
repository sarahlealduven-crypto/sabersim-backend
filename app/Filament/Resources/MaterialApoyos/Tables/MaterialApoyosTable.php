<?php

namespace App\Filament\Resources\MaterialApoyos\Tables;

use App\Models\MaterialApoyo;
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

class MaterialApoyosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('orden_visualizacion')
                    ->label('Orden')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('titulo')
                    ->label('Título')
                    ->searchable()
                    ->sortable()
                    ->description(fn (MaterialApoyo $record): string => $record->descripcion ?? '')
                    ->wrap(),

                TextColumn::make('materia.nombre')
                    ->label('Materia')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->placeholder('General')
                    ->color('primary'),

                TextColumn::make('tipo')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        MaterialApoyo::TIPO_YOUTUBE => 'YouTube',
                        MaterialApoyo::TIPO_GOOGLE_DRIVE => 'Google Drive',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        MaterialApoyo::TIPO_YOUTUBE => 'danger',
                        MaterialApoyo::TIPO_GOOGLE_DRIVE => 'info',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('duracion')
                    ->label('Duración')
                    ->placeholder('Sin dato')
                    ->toggleable(),

                TextColumn::make('source_url')
                    ->label('Fuente')
                    ->limit(45)
                    ->url(fn (MaterialApoyo $record): string => $record->source_url, shouldOpenInNewTab: true)
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('activo')
                    ->label('Publicado')
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
            ->defaultSort('orden_visualizacion')
            ->filters([
                SelectFilter::make('materia')
                    ->label('Materia')
                    ->relationship('materia', 'nombre')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('tipo')
                    ->label('Tipo')
                    ->options([
                        MaterialApoyo::TIPO_YOUTUBE => 'YouTube',
                        MaterialApoyo::TIPO_GOOGLE_DRIVE => 'Google Drive',
                    ]),

                TernaryFilter::make('activo')
                    ->label('Publicado')
                    ->placeholder('Todos los materiales')
                    ->trueLabel('Solo publicados')
                    ->falseLabel('Solo ocultos')
                    ->default(true),
            ], layout: FiltersLayout::AboveContent)
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('publish')
                        ->label('Publicar')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation(false)
                        ->action(fn ($records) => $records->each->update(['activo' => true])),
                    BulkAction::make('unpublish')
                        ->label('Ocultar')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation(false)
                        ->action(fn ($records) => $records->each->update(['activo' => false])),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
