<?php

namespace App\Filament\Resources\Users\RelationManagers;

use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class EstadisticasRelationManager extends RelationManager
{
    protected static string $relationship = 'estadisticasUsuario';

    protected static ?string $title = 'Estadísticas';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Resumen de estadísticas')
                    ->description('Estadísticas calculadas del usuario (solo lectura)')
                    ->schema([
                        Forms\Components\Select::make('materia_id')
                            ->label('Materia')
                            ->relationship('materia', 'nombre')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->disabled(),

                        Forms\Components\TextInput::make('total_examenes')
                            ->label('Total de exámenes')
                            ->numeric()
                            ->minValue(0)
                            ->disabled(),

                        Forms\Components\TextInput::make('total_preguntas_respondidas')
                            ->label('Total de preguntas')
                            ->numeric()
                            ->minValue(0)
                            ->disabled(),

                        Forms\Components\TextInput::make('respuestas_correctas')
                            ->label('Total correctas')
                            ->numeric()
                            ->minValue(0)
                            ->disabled(),
                    ])
                    ->columns(2),

                Section::make('Métricas de rendimiento')
                    ->description('Indicadores de rendimiento adicionales')
                    ->schema([
                        Forms\Components\TextInput::make('puntaje_promedio')
                            ->label('Puntaje promedio (%)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->step(0.01)
                            ->suffix('%')
                            ->disabled(),

                        Forms\Components\TextInput::make('mejor_puntaje')
                            ->label('Mejor puntaje (%)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->step(0.01)
                            ->suffix('%')
                            ->disabled(),

                        Forms\Components\TextInput::make('tiempo_total_gastado')
                            ->label('Tiempo total empleado (segundos)')
                            ->numeric()
                            ->minValue(0)
                            ->disabled()
                            ->formatStateUsing(fn ($state): string => $state ? self::formatTime($state) : '00:00'),
                    ])
                    ->columns(2),
            ])
            ->columns(1);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('materia.nombre')
            ->columns([
                Tables\Columns\TextColumn::make('materia.nombre')
                    ->label('Subject')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('total_examenes')
                    ->label('Exámenes')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('total_preguntas_respondidas')
                    ->label('Questions')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('respuestas_correctas')
                    ->label('Correctas')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('puntaje_promedio')
                    ->label('Promedio')
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn ($state): string => number_format($state, 2).'%')
                    ->badge()
                    ->color(fn ($state): string => ($state >= 70) ? 'success' : (($state >= 50) ? 'warning' : 'danger')),

                Tables\Columns\TextColumn::make('mejor_puntaje')
                    ->label('Best Score')
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn ($state): string => number_format($state, 2).'%')
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('tiempo_total_gastado')
                    ->label('Tiempo total')
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn ($state): string => $state ? self::formatTime($state) : '00:00'),

                Tables\Columns\TextColumn::make('fecha_ultimo_examen')
                    ->label('Last Exam')
                    ->dateTime()
                    ->sortable()
                    ->formatStateUsing(fn ($state): ?string => $state ? $state->format('d/m/Y H:i') : '-'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('materia')
                    ->label('Subject')
                    ->relationship('materia', 'nombre')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                ViewAction::make(),
            ])
            ->bulkActions([]);
    }

    protected function getTableEmptyStateHeading(): ?string
    {
        return 'No hay estadísticas';
    }

    protected function getTableEmptyStateDescription(): ?string
    {
        return 'Este usuario aún no ha realizado ningún examen.';
    }

    protected function getTableEmptyStateIcon(): ?string
    {
        return 'heroicon-o-chart-bar';
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
