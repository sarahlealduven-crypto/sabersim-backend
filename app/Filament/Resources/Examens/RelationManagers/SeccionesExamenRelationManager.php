<?php

namespace App\Filament\Resources\Examens\RelationManagers;

use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class SeccionesExamenRelationManager extends RelationManager
{
    protected static string $relationship = 'seccionesExamen';

    protected static ?string $title = 'Secciones del examen';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información de la sección')
                    ->schema([
                        Forms\Components\Select::make('materia_id')
                            ->label('Materia')
                            ->relationship('materia', 'nombre')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->disabled(),

                        Forms\Components\TextInput::make('total_preguntas')
                            ->label('Total de preguntas')
                            ->numeric()
                            ->minValue(1)
                            ->required()
                            ->disabled(),

                        Forms\Components\TextInput::make('respuestas_correctas')
                            ->label('Respuestas correctas')
                            ->numeric()
                            ->minValue(0)
                            ->required()
                            ->disabled(),

                        Forms\Components\TextInput::make('puntaje')
                            ->label('Puntaje (%)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->step(0.01)
                            ->suffix('%')
                            ->required()
                            ->disabled(),

                        Forms\Components\TextInput::make('tiempo_gastado')
                            ->label('Tiempo empleado (segundos)')
                            ->numeric()
                            ->minValue(0)
                            ->required()
                            ->disabled(),
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
                    ->label('Materia')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('total_preguntas')
                    ->label('Preguntas')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('respuestas_correctas')
                    ->label('Correctas')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('puntaje')
                    ->label('Score')
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn($state): string => number_format($state, 2) . '%')
                    ->badge()
                    ->color(fn($state): string => ($state >= 70) ? 'success' : (($state >= 50) ? 'warning' : 'danger')),

                Tables\Columns\TextColumn::make('tiempo_gastado')
                    ->label('Tiempo empleado')
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn($state): string => $state ? self::formatTime($state) : '00:00')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'asc')
            ->filters([
                Tables\Filters\SelectFilter::make('materia')
                    ->label('Materia')
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
        return 'No hay secciones en este examen';
    }

    protected function getTableEmptyStateDescription(): ?string
    {
        return 'Este examen aún no tiene secciones.';
    }

    protected function getTableEmptyStateIcon(): ?string
    {
        return 'heroicon-o-squares-2x2';
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
