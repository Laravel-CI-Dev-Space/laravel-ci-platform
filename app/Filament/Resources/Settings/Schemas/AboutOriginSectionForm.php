<?php

declare(strict_types=1);

namespace App\Filament\Resources\Settings\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AboutOriginSectionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Contenu')
                ->schema([
                    TextInput::make('eyebrow')
                        ->label('Eyebrow (petit texte au-dessus du titre)')
                        ->maxLength(100)
                        ->placeholder('Notre naissance'),

                    TextInput::make('title')
                        ->label('Titre')
                        ->required()
                        ->maxLength(200),

                    RichEditor::make('content')
                        ->label('Contenu')
                        ->required()
                        ->toolbarButtons([
                            'bold', 'italic', 'underline', 'strike',
                            'h2', 'h3',
                            'bulletList', 'orderedList',
                            'blockquote',
                            'link',
                            'undo', 'redo',
                        ])
                        ->columnSpanFull(),
                ]),

            Section::make('Média')
                ->columns(2)
                ->schema([
                    Select::make('media_type')
                        ->label('Type de média')
                        ->options([
                            'none'    => 'Aucun',
                            'image'   => 'Image',
                            'video'   => 'Vidéo locale',
                            'youtube' => 'YouTube',
                        ])
                        ->default('none')
                        ->required()
                        ->live(),

                    Select::make('media_position')
                        ->label('Position du média')
                        ->options([
                            'right' => 'Droite',
                            'left'  => 'Gauche',
                        ])
                        ->default('right')
                        ->visible(fn ($get) => $get('media_type') !== 'none'),

                    FileUpload::make('media_path')
                        ->label('Fichier (image ou vidéo)')
                        ->disk('assets')
                        ->directory('web/img/about')
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'video/mp4', 'video/webm'])
                        ->columnSpanFull()
                        ->visible(fn ($get) => in_array($get('media_type'), ['image', 'video'])),

                    TextInput::make('youtube_url')
                        ->label('URL YouTube embed')
                        ->url()
                        ->maxLength(255)
                        ->placeholder('https://www.youtube.com/embed/...')
                        ->columnSpanFull()
                        ->visible(fn ($get) => $get('media_type') === 'youtube'),

                    TextInput::make('caption')
                        ->label('Légende (optionnelle)')
                        ->maxLength(255)
                        ->columnSpanFull()
                        ->visible(fn ($get) => $get('media_type') !== 'none'),
                ]),

            Section::make('Affichage')
                ->schema([
                    Toggle::make('is_active')
                        ->label('Actif')
                        ->default(true),
                ]),
        ]);
    }
}
