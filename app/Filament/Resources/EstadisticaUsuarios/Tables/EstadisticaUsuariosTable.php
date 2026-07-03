<?php

namespace App\Filament\Resources\EstadisticaUsuarios\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class EstadisticaUsuariosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Estudiante')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->description(fn ($record): ?string => $record->user->email ?? ''),

                TextColumn::make('materia.nombre')
                    ->label('Subject')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),

                TextColumn::make('total_examenes')
                    ->label('Exámenes')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('primary'),

                TextColumn::make('total_preguntas_respondidas')
                    ->label('Questions')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('respuestas_correctas')
                    ->label('Correctas')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('puntaje_promedio')
                    ->label('Average')
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn ($state): string => number_format($state, 2).'%')
                    ->badge()
                    ->color(fn ($state): string => ($state >= 70) ? 'success' : (($state >= 50) ? 'warning' : 'danger')),

                TextColumn::make('mejor_puntaje')
                    ->label('Mejor puntaje')
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn ($state): string => number_format($state, 2).'%')
                    ->badge()
                    ->color('success'),

                TextColumn::make('tiempo_total_gastado')
                    ->label('Total Time')
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn ($state): string => $state ? self::formatTime($state) : '00:00'),

                TextColumn::make('fecha_ultimo_examen')
                    ->label('Último examen')
                    ->dateTime()
                    ->sortable()
                    ->formatStateUsing(fn ($state): ?string => $state ? $state->format('d/m/Y H:i') : '-'),

                TextColumn::make('created_at')
                    ->label('Created At')
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
                SelectFilter::make('user')
                    ->label('Estudiante')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('materia')
                    ->label('Subject')
                    ->relationship('materia', 'nombre')
                    ->searchable()
                    ->preload(),

                Filter::make('puntaje_promedio_range')
                    ->form(fn (Filter $filter): array => [
                        Forms\Components\TextInput::make('min_puntaje')
                            ->label('Puntaje mín. (%)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->step(0.01)
                            ->suffix('%'),
                        Forms\Components\TextInput::make('max_puntaje')
                            ->label('Puntaje máx. (%)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->step(0.01)
                            ->suffix('%'),
                    ])
                    ->query(function ($query, array $data): \Illuminate\Database\Eloquent\Builder {
                        return $query
                            ->when(
                                $data['min_puntaje'],
                                fn (\Illuminate\Database\Eloquent\Builder $query, $value): \Illuminate\Database\Eloquent\Builder => $query->where('puntaje_promedio', '>=', $value),
                            )
                            ->when(
                                $data['max_puntaje'],
                                fn (\Illuminate\Database\Eloquent\Builder $query, $value): \Illuminate\Database\Eloquent\Builder => $query->where('puntaje_promedio', '<=', $value),
                            );
                    }),
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

        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;

        if ($hours > 0) {
            return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
        }

        return sprintf('%02d:%02d', $minutes, $secs);
    }
}
