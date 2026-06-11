<?php

declare(strict_types=1);

namespace App\Filament\Resources\Companies\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CompanyForm
{
    public static function configure(Schema $schema, bool $withStatus = true): Schema
    {
        $fields = [
            TextInput::make('name')
                ->label('Nom')
                ->required()
                ->maxLength(255),

            TextInput::make('email')
                ->label('Email de réception des candidatures')
                ->email()
                ->maxLength(255)
                ->helperText('Les candidatures membres seront envoyées à cette adresse.'),

            Textarea::make('description')
                ->label('Description')
                ->rows(4)
                ->columnSpanFull(),

            TextInput::make('website')
                ->label('Site web')
                ->url()
                ->maxLength(255),

            FileUpload::make('logo')
                ->label('Logo')
                ->disk('public')
                ->directory('companies')
                ->image()
                ->fetchFileInformation(false)
                ->columnSpanFull(),
        ];

        if ($withStatus) {
            $fields[] = Toggle::make('is_active')
                ->label('Entreprise active')
                ->default(true)
                ->helperText('Une entreprise inactive n\'apparaît plus lors de la création d\'une nouvelle offre.');
        }

        return $schema->components([
            Section::make('Entreprise')
                ->columns(2)
                ->columnSpanFull()
                ->schema($fields),
        ]);
    }

    /**
     * Champs compacts pour la création / édition inline depuis une offre.
     *
     * @return array<int, TextInput|Textarea>
     */
    public static function inlineFields(): array
    {
        return [
            TextInput::make('name')
                ->label('Nom')
                ->required()
                ->maxLength(255),

            TextInput::make('email')
                ->label('Email de réception des candidatures')
                ->email()
                ->maxLength(255)
                ->helperText('Les candidatures membres seront envoyées à cette adresse.'),

            Textarea::make('description')
                ->label('Description')
                ->rows(3)
                ->columnSpanFull(),

            TextInput::make('website')
                ->label('Site web')
                ->url()
                ->maxLength(255),
        ];
    }
}
