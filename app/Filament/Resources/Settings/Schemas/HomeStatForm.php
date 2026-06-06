<?php

declare(strict_types=1);

namespace App\Filament\Resources\Settings\Schemas;

use App\Filament\Components\IconPickerInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class HomeStatForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Statistique')
                ->columns(2)
                ->schema([
                    IconPickerInput::make('icon')
                        ->label('Icône (Font Awesome)')
                        ->required()
                        ->columnSpanFull(),

                    TextInput::make('label')
                        ->label('Libellé')
                        ->required()
                        ->maxLength(100)
                        ->placeholder('Members'),

                    TextInput::make('suffix')
                        ->label('Suffixe')
                        ->maxLength(10)
                        ->default('+')
                        ->placeholder('+'),

                    TextInput::make('value')
                        ->label('Valeur manuelle')
                        ->numeric()
                        ->default(0)
                        ->helperText('Utilisée si auto-comptage désactivé.'),

                    TextInput::make('order')
                        ->label('Ordre')
                        ->numeric()
                        ->default(0),

                    Toggle::make('auto_count')
                        ->label('Auto-comptage depuis la base')
                        ->live()
                        ->columnSpanFull(),

                    TextInput::make('model')
                        ->label('Classe modèle (auto-comptage)')
                        ->maxLength(100)
                        ->placeholder('App\Models\User')
                        ->helperText('Nom de classe complet pour ::count().')
                        ->visible(fn ($get) => (bool) $get('auto_count'))
                        ->columnSpanFull(),

                    Toggle::make('is_active')
                        ->label('Actif')
                        ->default(true)
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
