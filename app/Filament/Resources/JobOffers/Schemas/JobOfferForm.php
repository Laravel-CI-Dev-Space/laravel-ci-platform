<?php

namespace App\Filament\Resources\JobOffers\Schemas;

use App\Enums\Jobs\JobOfferStatus;
use App\Enums\Jobs\JobOfferType;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class JobOfferForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            Section::make('Offre')
                ->columnSpanFull()
                ->columns(2)
                ->schema([
                    Select::make('company_id')
                        ->label('Entreprise')
                        ->relationship('company', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),

                    TextInput::make('title')
                        ->label('Titre')
                        ->required()
                        ->maxLength(255),

                    Textarea::make('description')
                        ->label('Description')
                        ->required()
                        ->rows(8)
                        ->columnSpanFull(),

                    TextInput::make('location')
                        ->label('Localisation')
                        ->required()
                        ->maxLength(120),

                    Select::make('type')
                        ->label('Type de contrat')
                        ->options(JobOfferType::options())
                        ->required(),

                    TextInput::make('salary')
                        ->label('Salaire')
                        ->maxLength(80),

                    DatePicker::make('deadline')
                        ->label('Date limite')
                        ->native(false),

                    Select::make('category_id')
                        ->label('Catégorie')
                        ->relationship('category', 'name')
                        ->searchable()
                        ->preload(),

                    Select::make('status')
                        ->label('Statut')
                        ->options(JobOfferStatus::options())
                        ->default(JobOfferStatus::DRAFT->value)
                        ->required(),

                    Select::make('skills')
                        ->label('Compétences')
                        ->relationship('skills', 'name')
                        ->multiple()
                        ->searchable()
                        ->preload()
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
