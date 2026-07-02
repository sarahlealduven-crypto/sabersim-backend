<?php

namespace App\Filament\Resources\Preguntas\Schemas;

use App\Enums\NivelDificultad;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PreguntaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Contenido de la pregunta')
                    ->description('La pregunta principal y contenido de apoyo')
                    ->schema([
                        Textarea::make('texto_pregunta')
                            ->label('Pregunta')
                            ->required()
                            ->rows(4)
                            ->columnSpanFull()
                            ->autofocus(),

                        RichEditor::make('texto_contexto')
                            ->label('Contexto')
                            ->helperText('Información de fondo adicional para la pregunta')
                            ->columnSpanFull(),

                        RichEditor::make('explicacion')
                            ->label('Explicación')
                            ->helperText('Explicación de la respuesta correcta')
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),

                Section::make('Clasificación')
                    ->description('Categoría y nivel de dificultad')
                    ->schema([
                        Select::make('materia_id')
                            ->label('Materia')
                            ->relationship('materia', 'nombre')
                            ->searchable()
                            ->preload()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $set('topico_id', null);
                            })
                            ->required(),

                        Select::make('topico_id')
                            ->label('Tema')
                            ->relationship('topico', 'nombre')
                            ->searchable()
                            ->preload()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $get) {
                                if ($get('topico_id') && ! $get('texto_pregunta')) {
                                    // Auto-populate question if needed
                                }
                            })
                            ->required(),

                        Select::make('nivel_dificultad')
                            ->label('Nivel de dificultad')
                            ->options(NivelDificultad::class)
                            ->required()
                            ->default(NivelDificultad::Medio),

                        Toggle::make('activo')
                            ->label('Activa')
                            ->inline(false)
                            ->default(true),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('Opciones de respuesta')
                    ->description('Opciones de respuesta tipo test. Exactamente una debe marcarse como correcta.')
                    ->schema([
                        Repeater::make('opcionesRespuesta')
                            ->label('Opciones de respuesta')
                            ->relationship('opcionesRespuesta')
                            ->schema([
                                Forms\Components\TextInput::make('letra_opcion')
                                    ->label('Letra de opción')
                                    ->disabled()
                                    ->helperText('Se asigna automáticamente (A, B, C, D, E, F)'),

                                Textarea::make('texto_opcion')
                                    ->label('Texto de la opción')
                                    ->required()
                                    ->rows(2)
                                    ->columnSpanFull(),

                                Toggle::make('es_correcta')
                                    ->label('Respuesta correcta')
                                    ->inline(false)
                                    ->reactive()
                                    ->live(),
                            ])
                            ->columns(2)
                            ->itemLabel(fn (array $state): ?string => trim(($state['letra_opcion'] ?? '').'. '.($state['texto_opcion'] ?? '')) ?: null)
                            ->minItems(2)
                            ->maxItems(6)
                            ->defaultItems(4)
                            ->collapsible()
                            ->cloneable()
                            ->reorderableWithButtons()
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
