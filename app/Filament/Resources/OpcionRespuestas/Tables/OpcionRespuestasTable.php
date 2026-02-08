<?php

namespace App\Filament\Resources\OpcionRespuestas\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class OpcionRespuestasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('pregunta.texto_pregunta')
                    ->label('Pregunta')
                    ->searchable()
                    ->limit(50)
                    ->wrap()
                    ->description(fn($record): ?string => $record->pregunta->topico->materia->nombre ?? '')
                    ->sortable(),

                TextColumn::make('letra_opcion')
                    ->label('Letra')
                    ->badge()
                    ->color('primary')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('texto_opcion')
                    ->label('Texto de la opción')
                    ->searchable()
                    ->limit(100)
                    ->wrap(),

                IconColumn::make('es_correcta')
                    ->label('Correcta')
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
            ->defaultSort('letra_opcion', 'asc')
            ->filters([
                TernaryFilter::make('es_correcta')
                    ->label('Correcta')
                    ->placeholder('Todas las opciones')
                    ->trueLabel('Solo correctas')
                    ->falseLabel('Solo incorrectas'),
            ])
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
