<?php

namespace App\Filament\Resources\Materias\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class MateriasTable
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

                TextColumn::make('nombre')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable()
                    ->description(fn($record): string => $record->descripcion ?? ''),

                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                ImageColumn::make('icono')
                    ->label('Icono')
                    ->circular()
                    ->defaultImageUrl(fn() => asset('images/placeholder-subject.svg'))
                    ->size(40),

                TextColumn::make('cantidad_preguntas')
                    ->label('Preguntas')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('primary'),

                TextColumn::make('tiempo_limite')
                    ->label('Tiempo límite')
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn($state): string => $state ? $state . ' min' : 'Sin límite'),

                IconColumn::make('activo')
                    ->label('Activa')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                TextColumn::make('topicos_count')
                    ->label('Temas')
                    ->numeric()
                    ->sortable()
                    ->counts('topicos')
                    ->badge()
                    ->color('warning'),

                TextColumn::make('preguntas_count')
                    ->label('Cantidad de preguntas')
                    ->numeric()
                    ->sortable()
                    ->counts('preguntas')
                    ->badge()
                    ->color('info'),

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
            ->defaultSort('orden_visualizacion', 'asc')
            ->filters([
                TernaryFilter::make('activo')
                    ->label('Activa')
                    ->placeholder('Todas las materias')
                    ->trueLabel('Solo activas')
                    ->falseLabel('Solo inactivas')
                    ->default(true),
            ], layout: FiltersLayout::AboveContent)
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
