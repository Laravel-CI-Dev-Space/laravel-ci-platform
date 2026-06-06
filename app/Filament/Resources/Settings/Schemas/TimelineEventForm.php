<?php

declare(strict_types=1);

namespace App\Filament\Resources\Settings\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TimelineEventForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Étape de la timeline')
                ->columns(2)
                ->schema([
                    TextInput::make('period')
                        ->label('Période')
                        ->required()
                        ->maxLength(50)
                        ->placeholder('Jan 2026'),

                    TextInput::make('order')
                        ->label('Ordre')
                        ->numeric()
                        ->default(0),

                    TextInput::make('title')
                        ->label('Titre')
                        ->required()
                        ->maxLength(150)
                        ->columnSpanFull(),

                    Textarea::make('description')
                        ->label('Description')
                        ->required()
                        ->rows(4)
                        ->columnSpanFull(),

                    Toggle::make('is_active')
                        ->label('Actif')
                        ->default(true),
                ]),
        ]);
    }
}
