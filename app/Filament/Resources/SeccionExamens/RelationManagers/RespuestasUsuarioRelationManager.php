<?php

namespace App\Filament\Resources\SeccionExamens\RelationManagers;

use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class RespuestasUsuarioRelationManager extends RelationManager
{
    protected static string $relationship = 'respuestas_usuario';

    protected static ?string $title = 'Respuestas del usuario';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detalles de la respuesta')
                    ->schema([
                        Forms\Components\Select::make('pregunta_id')
                            ->label('Pregunta')
                            ->relationship('pregunta', 'texto_pregunta')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->disabled(),

                        Forms\Components\Select::make('opcion_seleccionada_id')
                            ->label('Respuesta seleccionada')
                            ->relationship('opcionSeleccionada', 'texto_opcion')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->disabled(),

                        Forms\Components\Toggle::make('es_correcta')
                            ->label('Es correcta')
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
            ->recordTitleAttribute('pregunta.texto_pregunta')
            ->columns([
                Tables\Columns\TextColumn::make('pregunta.texto_pregunta')
                    ->label('Pregunta')
                    ->searchable()
                    ->limit(50)
                    ->wrap(),

                Tables\Columns\TextColumn::make('opcionSeleccionada.letra_opcion')
                    ->label('Selected')
                    ->badge()
                    ->color('info')
                    ->description(fn($record): ?string => $record->opcionSeleccionada->texto_opcion ?? ''),

                Tables\Columns\IconColumn::make('es_correcta')
                    ->label('Correct')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('tiempo_gastado')
                    ->label('Tiempo empleado')
                    ->numeric()
                    ->formatStateUsing(fn($state): string => $state ? self::formatTime($state) : '00:00')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Respondido el')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'asc')
            ->filters([
                Tables\Filters\TernaryFilter::make('es_correcta')
                    ->label('Correct')
                    ->placeholder('All answers')
                    ->trueLabel('Correct only')
                    ->falseLabel('Incorrect only'),
            ])
            ->actions([
                ViewAction::make(),
            ])
            ->bulkActions([]);
    }

    protected function getTableEmptyStateHeading(): ?string
    {
        return 'No hay respuestas registradas';
    }

    protected function getTableEmptyStateDescription(): ?string
    {
        return 'Esta sección del examen aún no tiene respuestas registradas.';
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

        $minutes = floor($seconds / 60);
        $secs = $seconds % 60;

        return sprintf('%02d:%02d', $minutes, $secs);
    }
}
