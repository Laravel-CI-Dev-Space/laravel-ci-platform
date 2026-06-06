<?php

declare(strict_types=1);

namespace App\Filament\Resources\Settings\Schemas;

use App\Filament\Components\IconPickerInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CommunityValueForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Valeur communautaire')
                ->columns(2)
                ->schema([
                    IconPickerInput::make('icon')
                        ->label('Icône (Font Awesome)')
                        ->required()
                        ->columnSpanFull(),

                    TextInput::make('title')
                        ->label('Titre')
                        ->required()
                        ->maxLength(100),

                    TextInput::make('order')
                        ->label('Ordre')
                        ->numeric()
                        ->default(0),

                    Textarea::make('description')
                        ->label('Description')
                        ->required()
                        ->rows(3)
                        ->columnSpanFull(),

                    Toggle::make('is_active')
                        ->label('Actif')
                        ->default(true),
                ]),
        ]);
    }
}
