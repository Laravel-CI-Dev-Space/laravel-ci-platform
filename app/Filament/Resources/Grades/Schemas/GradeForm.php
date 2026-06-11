<?php

declare(strict_types=1);

namespace App\Filament\Resources\Grades\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class GradeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informations du grade')
                ->columns(2)
                ->schema([
                    TextInput::make('name')
                        ->label('Nom')
                        ->required()
                        ->maxLength(100)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),

                    TextInput::make('slug')
                        ->label('Slug')
                        ->required()
                        ->maxLength(120)
                        ->unique(ignoreRecord: true),

                    TextInput::make('min_points')
                        ->label('Points minimum')
                        ->numeric()
                        ->required()
                        ->minValue(0)
                        ->helperText('Nombre de points de réputation à partir duquel ce grade est attribué.'),

                    TextInput::make('order')
                        ->label('Ordre')
                        ->numeric()
                        ->required()
                        ->helperText('Plus la valeur est élevée, plus le grade est prestigieux (le super-admin reçoit le grade ayant le plus grand ordre).'),

                    ColorPicker::make('color')
                        ->label('Couleur')
                        ->required(),

                    TextInput::make('icon')
                        ->label('Icône (classe Font Awesome)')
                        ->required()
                        ->maxLength(60)
                        ->helperText('Exemple : fa-solid fa-trophy'),

                    Textarea::make('description')
                        ->label('Description')
                        ->rows(2)
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
