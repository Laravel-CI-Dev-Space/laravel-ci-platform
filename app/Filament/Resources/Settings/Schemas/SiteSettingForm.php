<?php

declare(strict_types=1);

namespace App\Filament\Resources\Settings\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SiteSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Paramètre du site')
                ->columns(2)
                ->schema([
                    TextInput::make('key')
                        ->label('Clé')
                        ->required()
                        ->maxLength(150)
                        ->unique(ignoreRecord: true)
                        ->disabledOn('edit')
                        ->helperText('Identifiant unique. Non modifiable après création.'),

                    Select::make('group')
                        ->label('Groupe')
                        ->options([
                            'general'  => 'Général',
                            'identity' => 'Identité & Logos',
                            'home'     => 'Accueil',
                            'about'    => 'À propos',
                            'footer'   => 'Footer',
                            'social'   => 'Réseaux sociaux',
                            'seo'      => 'SEO',
                        ])
                        ->required()
                        ->default('general'),

                    TextInput::make('label')
                        ->label('Libellé')
                        ->required()
                        ->maxLength(150)
                        ->columnSpanFull(),

                    Select::make('type')
                        ->label('Type')
                        ->options([
                            'text'     => 'Texte court',
                            'textarea' => 'Texte long',
                            'image'    => 'Image',
                            'boolean'  => 'Oui / Non',
                            'number'   => 'Nombre',
                            'color'    => 'Couleur',
                            'url'      => 'URL',
                            'video'    => 'Vidéo',
                        ])
                        ->required()
                        ->default('text')
                        ->live(),

                    TextInput::make('order')
                        ->label('Ordre')
                        ->numeric()
                        ->default(0),

                    Textarea::make('description')
                        ->label('Description')
                        ->rows(2)
                        ->columnSpanFull(),

                    TextInput::make('value')
                        ->label('Valeur')
                        ->columnSpanFull()
                        ->visible(fn ($get) => in_array($get('type'), ['text', 'url', 'color', 'number'])),

                    Textarea::make('value')
                        ->label('Valeur')
                        ->rows(4)
                        ->columnSpanFull()
                        ->visible(fn ($get) => $get('type') === 'textarea'),

                    Toggle::make('value')
                        ->label('Valeur')
                        ->columnSpanFull()
                        ->visible(fn ($get) => $get('type') === 'boolean'),

                    FileUpload::make('value')
                        ->label('Image')
                        ->image()
                        ->disk('assets')
                        ->directory('web/img')
                        ->columnSpanFull()
                        ->visible(fn ($get) => $get('type') === 'image'),
                ]),
        ]);
    }
}
