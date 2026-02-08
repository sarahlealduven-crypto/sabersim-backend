<?php

namespace App\Filament\Resources\Users\RelationManagers;

use App\Enums\EstadoExamen;
use App\Enums\TipoExamen;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class ExamenesRelationManager extends RelationManager
{
    protected static string $relationship = 'examenes';

    protected static ?string $title = 'Exámenes';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información del examen')
                    ->schema([
                        Forms\Components\Select::make('tipo_examen')
                            ->label('Tipo de examen')
                            ->options(TipoExamen::class)
                            ->required()
                            ->default(TipoExamen::Completo)
                            ->disabled(),

                        Forms\Components\Select::make('estado')
                            ->label('Estado')
                            ->options(EstadoExamen::class)
                            ->required()
                            ->default(EstadoExamen::EnProgreso)
                            ->disabled(),

                        Forms\Components\DateTimePicker::make('fecha_inicio')
                            ->label('Iniciado el')
                            ->seconds(false)
                            ->disabled(),

                        Forms\Components\DateTimePicker::make('fecha_completado')
                            ->label('Completado el')
                            ->seconds(false)
                            ->disabled(),
                    ])
                    ->columns(2),

                Section::make('Resultados')
                    ->schema([
                        Forms\Components\TextInput::make('puntaje_total')
                            ->label('Puntaje total (%)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->step(0.01)
                            ->suffix('%')
                            ->disabled(),

                        Forms\Components\TextInput::make('tiempo_gastado')
                            ->label('Tiempo empleado (segundos)')
                            ->numeric()
                            ->minValue(0)
                            ->disabled(),
                    ])
                    ->columns(2),
            ])
            ->columns(1);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('tipo_examen')
                    ->label('Type')
                    ->badge()
                    ->color(fn(TipoExamen $state): string => match ($state) {
                        TipoExamen::Completo => 'primary',
                        TipoExamen::PorMateria => 'warning',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn(EstadoExamen $state): string => match ($state) {
                        EstadoExamen::EnProgreso => 'info',
                        EstadoExamen::Completado => 'success',
                        EstadoExamen::Abandonado => 'danger',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('fecha_inicio')
                    ->label('Started')
                    ->dateTime()
                    ->sortable()
                    ->formatStateUsing(fn($state): ?string => $state ? $state->format('d/m/Y H:i') : '-'),

                Tables\Columns\TextColumn::make('fecha_completado')
                    ->label('Completado')
                    ->dateTime()
                    ->sortable()
                    ->formatStateUsing(fn($state): ?string => $state ? $state->format('d/m/Y H:i') : '-'),

                Tables\Columns\TextColumn::make('puntaje_total')
                    ->label('Score')
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn($state): string => $state ? $state . '%' : '-')
                    ->badge()
                    ->color(fn($state): string => ($state >= 70) ? 'success' : (($state >= 50) ? 'warning' : 'danger')),

                Tables\Columns\TextColumn::make('tiempo_gastado')
                    ->label('Tiempo empleado')
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn($state): ?string => $state ? self::formatTime($state) : '-'),

                Tables\Columns\TextColumn::make('secciones_count')
                    ->label('Secciones')
                    ->numeric()
                    ->sortable()
                    ->counts('seccionesExamen')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('tipo_examen')
                    ->label('Tipo')
                    ->options(TipoExamen::class),

                Tables\Filters\SelectFilter::make('estado')
                    ->label('Estado')
                    ->options(EstadoExamen::class),
            ])
            ->actions([
                ViewAction::make(),
                DeleteAction::make()
                    ->visible(fn($record): bool => $record->estado === EstadoExamen::Abandonado),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('delete_abandoned')
                        ->label('Delete Abandoned')
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

    protected function getTableEmptyStateHeading(): ?string
    {
        return 'No hay exámenes';
    }

    protected function getTableEmptyStateDescription(): ?string
    {
        return 'Este usuario aún no ha realizado ningún examen.';
    }

    protected function getTableEmptyStateIcon(): ?string
    {
        return 'heroicon-o-document-text';
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
