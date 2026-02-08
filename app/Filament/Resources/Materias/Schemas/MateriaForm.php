<?php

namespace App\Filament\Resources\Materias\Schemas;

use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class MateriaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información básica')
                    ->description('Datos principales de la materia')
                    ->schema([
                        TextInput::make('nombre')
                            ->label('Nombre de la materia')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->autofocus(),

                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText('Se generará automáticamente a partir del nombre'),

                        Textarea::make('descripcion')
                            ->label('Descripción')
                            ->rows(3)
                            ->columnSpanFull(),

                        FileUpload::make('icono')
                            ->label('Icono')
                            ->image()
                            ->directory('materias')
                            ->maxSize(1024)
                            ->imageEditor()
                            ->helperText('Sube un icono para esta materia'),
                    ])
                    ->columns(2),

                Section::make('Configuración')
                    ->description('Opciones para la configuración del examen')
                    ->schema([
                        TextInput::make('cantidad_preguntas')
                            ->label('Cantidad de preguntas')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->default(10),

                        TextInput::make('tiempo_limite')
                            ->label('Tiempo límite (minutos)')
                            ->numeric()
                            ->minValue(0)
                            ->helperText('0 significa sin límite de tiempo'),

                        TextInput::make('orden_visualizacion')
                            ->label('Orden de visualización')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->helperText('Los números menores se muestran primero'),

                        Toggle::make('activo')
                            ->label('Activa')
                            ->inline(false)
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }
}
