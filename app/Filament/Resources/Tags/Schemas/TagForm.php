<?php

namespace App\Filament\Resources\Tags\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class TagForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informations du tag')
                ->columns(2)
                ->schema([
                    TextInput::make('name')
                        ->label('Nom')
                        ->required()
                        ->maxLength(50)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),

                    TextInput::make('slug')
                        ->label('Slug')
                        ->required()
                        ->maxLength(60)
                        ->unique(ignoreRecord: true),

                    Select::make('scope')
                        ->label('Portée')
                        ->options([
                            'forum' => 'Forum uniquement',
                            'blog'  => 'Blog uniquement',
                            'both'  => 'Forum & Blog',
                        ])
                        ->default('both')
                        ->required(),

                    ColorPicker::make('color')
                        ->label('Couleur'),
                ]),
        ]);
    }
}
