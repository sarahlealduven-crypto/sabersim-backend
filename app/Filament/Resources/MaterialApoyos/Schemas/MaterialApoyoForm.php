<?php

namespace App\Filament\Resources\MaterialApoyos\Schemas;

use App\Models\MaterialApoyo;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class MaterialApoyoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Contenido')
                    ->description('Datos visibles del material en la biblioteca de apoyo')
                    ->schema([
                        Select::make('materia_id')
                            ->label('Materia')
                            ->relationship('materia', 'nombre')
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        TextInput::make('titulo')
                            ->label('Título')
                            ->required()
                            ->maxLength(160)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (?string $state, callable $set, callable $get): void {
                                if (! $get('slug')) {
                                    $set('slug', Str::slug($state ?? ''));
                                }
                            })
                            ->autofocus(),

                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(180)
                            ->unique(ignoreRecord: true)
                            ->helperText('Identificador usado por la URL pública del material.'),

                        Textarea::make('descripcion')
                            ->label('Descripción')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Embed externo')
                    ->description('El contenido se muestra desde YouTube o Google Drive; no se guardan archivos en el servidor')
                    ->schema([
                        Select::make('tipo')
                            ->label('Tipo')
                            ->options([
                                MaterialApoyo::TIPO_YOUTUBE => 'YouTube',
                                MaterialApoyo::TIPO_GOOGLE_DRIVE => 'Google Drive',
                            ])
                            ->required()
                            ->native(false)
                            ->default(MaterialApoyo::TIPO_YOUTUBE),

                        TextInput::make('source_url')
                            ->label('URL fuente')
                            ->required()
                            ->url()
                            ->maxLength(2048)
                            ->helperText('Acepta enlaces de YouTube, youtu.be, Google Drive, Docs, Slides o Sheets.'),

                        TextInput::make('embed_url')
                            ->label('URL embebida')
                            ->disabled()
                            ->dehydrated(false)
                            ->maxLength(2048)
                            ->helperText('Se genera automáticamente al guardar.'),

                        TextInput::make('thumbnail_url')
                            ->label('URL de miniatura')
                            ->url()
                            ->maxLength(2048)
                            ->helperText('Opcional. Útil para videos de YouTube.'),
                    ])
                    ->columns(2),

                Section::make('Publicación')
                    ->description('Orden y estado de publicación del material')
                    ->schema([
                        TextInput::make('duracion')
                            ->label('Duración o formato')
                            ->maxLength(50)
                            ->helperText('Ejemplo: 32 min, Video, Guía.'),

                        TextInput::make('orden_visualizacion')
                            ->label('Orden de visualización')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->helperText('Los números menores se muestran primero.'),

                        Toggle::make('activo')
                            ->label('Publicado')
                            ->inline(false)
                            ->default(true),
                    ])
                    ->columns(3),
            ]);
    }
}
