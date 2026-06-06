<?php

declare(strict_types=1);

namespace App\Filament\Resources\Settings\Schemas;

use App\Filament\Components\IconPickerInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PartnerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Partenaire')
                ->columns(2)
                ->schema([
                    TextInput::make('name')
                        ->label('Nom')
                        ->required()
                        ->maxLength(150)
                        ->columnSpanFull(),

                    Select::make('type')
                        ->label('Type')
                        ->options([
                            'community'     => 'Communauté',
                            'sponsor'       => 'Sponsor',
                            'institutional' => 'Institutionnel',
                        ])
                        ->required()
                        ->default('community'),

                    TextInput::make('url')
                        ->label('URL')
                        ->url()
                        ->maxLength(255),

                    FileUpload::make('logo')
                        ->label('Logo')
                        ->image()
                        ->disk('assets')
                        ->directory('web/img/partners')
                        ->columnSpanFull()
                        ->helperText('Laissez vide pour utiliser l\'icône Font Awesome.'),

                    IconPickerInput::make('icon')
                        ->label('Icône Font Awesome (fallback si pas de logo)')
                        ->columnSpanFull(),

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
