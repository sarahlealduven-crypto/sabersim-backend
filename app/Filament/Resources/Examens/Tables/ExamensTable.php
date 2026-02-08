<?php

namespace App\Filament\Resources\Examens\Tables;

use App\Enums\EstadoExamen;
use App\Enums\TipoExamen;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ExamensTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('user.name')
                    ->label('Estudiante')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                TextColumn::make('tipo_examen')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn(TipoExamen $state): string => match ($state) {
                        TipoExamen::Completo => 'primary',
                        TipoExamen::PorMateria => 'warning',
                    })
                    ->sortable(),

                TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn(EstadoExamen $state): string => match ($state) {
                        EstadoExamen::EnProgreso => 'info',
                        EstadoExamen::Completado => 'success',
                        EstadoExamen::Abandonado => 'danger',
                    })
                    ->sortable(),

                TextColumn::make('fecha_inicio')
                    ->label('Iniciado')
                    ->dateTime()
                    ->sortable()
                    ->formatStateUsing(fn($state): ?string => $state ? $state->format('d/m/Y H:i') : '-'),

                TextColumn::make('fecha_completado')
                    ->label('Completado')
                    ->dateTime()
                    ->sortable()
                    ->formatStateUsing(fn($state): ?string => $state ? $state->format('d/m/Y H:i') : '-'),

                TextColumn::make('puntaje_total')
                    ->label('Puntaje')
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn($state): string => $state ? $state . '%' : '-')
                    ->badge()
                    ->color(fn($state): string => ($state >= 70) ? 'success' : (($state >= 50) ? 'warning' : 'danger')),

                TextColumn::make('tiempo_gastado')
                    ->label('Tiempo empleado')
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn($state): ?string => $state ? self::formatTime($state) : '-'),

                TextColumn::make('secciones_count')
                    ->label('Secciones')
                    ->numeric()
                    ->sortable()
                    ->counts('seccionesExamen')
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
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('user')
                    ->label('Estudiante')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('tipo_examen')
                    ->label('Tipo')
                    ->options(TipoExamen::class),

                SelectFilter::make('estado')
                    ->label('Estado')
                    ->options(EstadoExamen::class),

                Filter::make('fecha_range')
                    ->form(fn(Filter $filter): array => [
                        Forms\Components\DateTimePicker::make('fecha_desde')
                            ->label('Desde'),
                        Forms\Components\DateTimePicker::make('fecha_hasta')
                            ->label('Hasta'),
                    ])
                    ->query(function ($query, array $data): Builder {
                        return $query
                            ->when(
                                $data['fecha_desde'],
                                fn(Builder $query, $date): Builder => $query->whereDate('fecha_inicio', '>=', $date),
                            )
                            ->when(
                                $data['fecha_hasta'],
                                fn(Builder $query, $date): Builder => $query->whereDate('fecha_inicio', '<=', $date),
                            );
                    }),
            ], layout: FiltersLayout::AboveContent)
            ->recordActions([
                ViewAction::make(),
                DeleteAction::make()
                    ->visible(fn($record): bool => $record->estado === EstadoExamen::Abandonado),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('delete_abandoned')
                        ->label('Eliminar abandonados')
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion()
                        ->action(function ($records) {
                            $records->filter(fn($record): bool => $record->estado === EstadoExamen::Abandonado)
                                ->each->delete();
                        }),
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
