<?php

namespace App\Filament\Resources\Materias\RelationManagers;

use App\Enums\NivelDificultad;
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

class PreguntasRelationManager extends RelationManager
{
    protected static string $relationship = 'preguntas';

    protected static ?string $title = 'Questions';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Question Content')
                    ->schema([
                        Forms\Components\Textarea::make('texto_pregunta')
                            ->label('Question')
                            ->required()
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\RichEditor::make('texto_contexto')
                            ->label('Context')
                            ->columnSpanFull(),

                        Forms\Components\RichEditor::make('explicacion')
                            ->label('Explanation')
                            ->columnSpanFull(),
                    ]),

                Section::make('Classification')
                    ->schema([
                        Forms\Components\Select::make('topico_id')
                            ->label('Topic')
                            ->relationship('topico', 'nombre')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make('nivel_dificultad')
                            ->label('Difficulty Level')
                            ->options(NivelDificultad::class)
                            ->required()
                            ->default(NivelDificultad::Medio),

                        Forms\Components\Toggle::make('activo')
                            ->label('Active')
                            ->default(true),
                    ])
                    ->columns(2),
            ])
            ->columns(1);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('texto_pregunta')
            ->columns([
                Tables\Columns\TextColumn::make('texto_pregunta')
                    ->label('Pregunta')
                    ->searchable()
                    ->limit(50)
                    ->wrap(),

                Tables\Columns\TextColumn::make('topico.nombre')
                    ->label('Topic')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('warning'),

                Tables\Columns\TextColumn::make('nivel_dificultad')
                    ->label('Dificultad')
                    ->badge()
                    ->color(fn(NivelDificultad $state): string => match ($state) {
                        NivelDificultad::Facil => 'success',
                        NivelDificultad::Medio => 'warning',
                        NivelDificultad::Dificil => 'danger',
                    }),

                Tables\Columns\TextColumn::make('opciones_count')
                    ->label('Options')
                    ->counts('opcionesRespuesta')
                    ->badge()
                    ->color('info'),

                Tables\Columns\IconColumn::make('activo')
                    ->label('Activa')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('topico')
                    ->relationship('topico', 'nombre'),

                Tables\Filters\SelectFilter::make('nivel_dificultad')
                    ->options(NivelDificultad::class),

                Tables\Filters\TernaryFilter::make('activo')
                    ->label('Activa')
                    ->placeholder('Todas las preguntas')
                    ->trueLabel('Solo activas')
                    ->falseLabel('Solo inactivas')
                    ->default(true),
            ])
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
        return 'No hay preguntas';
    }

    protected function getTableEmptyStateDescription(): ?string
    {
        return 'Añade preguntas a esta materia.';
    }

    protected function getTableEmptyStateIcon(): ?string
    {
        return 'heroicon-o-question-mark-circle';
    }
}
