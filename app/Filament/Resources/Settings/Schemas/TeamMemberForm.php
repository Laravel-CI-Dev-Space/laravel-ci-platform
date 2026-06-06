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

class TeamMemberForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Identité')
                ->columns(2)
                ->schema([
                    TextInput::make('first_name')
                        ->label('Prénom')
                        ->required()
                        ->maxLength(100),

                    TextInput::make('last_name')
                        ->label('Nom')
                        ->required()
                        ->maxLength(100),

                    TextInput::make('role')
                        ->label('Rôle')
                        ->required()
                        ->maxLength(150)
                        ->placeholder('Founder & Architect')
                        ->columnSpanFull(),

                    Textarea::make('bio')
                        ->label('Bio')
                        ->rows(3)
                        ->maxLength(1000)
                        ->columnSpanFull(),
                ]),

            Section::make('Avatar')
                ->columns(2)
                ->schema([
                    FileUpload::make('avatar')
                        ->label('Photo (optionnelle)')
                        ->image()
                        ->disk('assets')
                        ->directory('web/img/team')
                        ->columnSpanFull()
                        ->helperText('Si absente, les initiales et la couleur seront utilisées.'),

                    TextInput::make('avatar_initials')
                        ->label('Initiales (fallback)')
                        ->maxLength(5)
                        ->placeholder('SB'),

                    Select::make('avatar_color')
                        ->label('Couleur avatar')
                        ->options([
                            'av-1' => 'Couleur 1 (bleu)',
                            'av-2' => 'Couleur 2 (vert)',
                            'av-3' => 'Couleur 3 (orange)',
                            'av-4' => 'Couleur 4 (rouge)',
                            'av-5' => 'Couleur 5 (violet)',
                            'av-6' => 'Couleur 6 (gris)',
                        ])
                        ->default('av-1'),
                ]),

            Section::make('Liens sociaux')
                ->columns(3)
                ->schema([
                    TextInput::make('github_url')
                        ->label('GitHub')
                        ->url()
                        ->maxLength(255),

                    TextInput::make('linkedin_url')
                        ->label('LinkedIn')
                        ->url()
                        ->maxLength(255),

                    TextInput::make('twitter_url')
                        ->label('Twitter / X')
                        ->url()
                        ->maxLength(255),
                ]),

            Section::make('Affichage')
                ->columns(2)
                ->schema([
                    TextInput::make('order')
                        ->label('Ordre')
                        ->numeric()
                        ->default(0),

                    Toggle::make('is_active')
                        ->label('Actif')
                        ->default(true),
                ]),
        ]);
    }
}
