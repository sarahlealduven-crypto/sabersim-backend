<?php

namespace App\Filament\Resources\Preguntas\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class OpcionesRespuestaRelationManager extends RelationManager
{
    protected static string $relationship = 'opcionesRespuesta';

    protected static ?string $title = 'Answer Options';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Option Details')
                    ->schema([
                        Forms\Components\Select::make('letra_opcion')
                            ->label('Option Letter')
                            ->options([
                                'A' => 'A',
                                'B' => 'B',
                                'C' => 'C',
                                'D' => 'D',
                                'E' => 'E',
                                'F' => 'F',
                            ])
                            ->required()
                            ->default('A'),

                        Forms\Components\Textarea::make('texto_opcion')
                            ->label('Option Text')
                            ->required()
                            ->rows(2)
                            ->columnSpanFull(),

                        Forms\Components\Toggle::make('es_correcta')
                            ->label('Correct Answer')
                            ->inline(false)
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                // Ensure only one option is marked as correct
                                if ($state) {
                                    // Logic will be handled by observer
                                }
                            })
                            ->live(),
                    ])
                    ->columns(2),
            ])
            ->columns(1);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('texto_opcion')
            ->reorderable('orden')
            ->columns([
                Tables\Columns\TextColumn::make('letra_opcion')
                    ->label('Letra')
                    ->badge()
                    ->color('primary')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('texto_opcion')
                    ->label('Option Text')
                    ->searchable()
                    ->limit(50)
                    ->wrap(),

                Tables\Columns\IconColumn::make('es_correcta')
                    ->label('Correcta')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('letra_opcion', 'asc')
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    protected function getTableEmptyStateHeading(): ?string
    {
        return 'No hay opciones de respuesta';
    }

    protected function getTableEmptyStateDescription(): ?string
    {
        return 'Añade opciones de respuesta para esta pregunta. Se requieren al menos 2 opciones.';
    }

    protected function getTableEmptyStateIcon(): ?string
    {
        return 'heroicon-o-document-text';
    }
}
