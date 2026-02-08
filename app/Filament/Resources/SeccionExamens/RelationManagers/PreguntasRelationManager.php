<?php

namespace App\Filament\Resources\SeccionExamens\RelationManagers;

use App\Enums\NivelDificultad;
use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
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
                        Forms\Components\Toggle::make('activo')
                            ->label('Active')
                            ->inline(false)
                            ->default(true),

                        Forms\Components\Select::make('nivel_dificultad')
                            ->label('Difficulty Level')
                            ->options(NivelDificultad::class)
                            ->required()
                            ->default(NivelDificultad::Medio),
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

                Tables\Columns\TextColumn::make('nivel_dificultad')
                    ->label('Dificultad')
                    ->badge()
                    ->color(fn(NivelDificultad $state): string => match ($state) {
                        NivelDificultad::Facil => 'success',
                        NivelDificultad::Medio => 'warning',
                        NivelDificultad::Dificil => 'danger',
                    }),

                Tables\Columns\TextColumn::make('opciones_count')
                    ->label('Opciones')
                    ->counts('opcionesRespuesta')
                    ->badge()
                    ->color('info'),

                Tables\Columns\IconColumn::make('activo')
                    ->label('Active')
                    ->boolean(),
            ])
            ->defaultSort('texto_pregunta', 'asc')
            ->filters([
                Tables\Filters\SelectFilter::make('nivel_dificultad')
                    ->options(NivelDificultad::class),
            ])
            ->headerActions([
                AttachAction::make(),
            ])
            ->actions([
                ViewAction::make(),
                DetachAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DetachBulkAction::make(),
                ]),
            ]);
    }

    protected function getTableEmptyStateHeading(): ?string
    {
        return 'No hay preguntas';
    }

    protected function getTableEmptyStateDescription(): ?string
    {
        return 'Asocia preguntas a esta sección del examen.';
    }

    protected function getTableEmptyStateIcon(): ?string
    {
        return 'heroicon-o-question-mark-circle';
    }
}
