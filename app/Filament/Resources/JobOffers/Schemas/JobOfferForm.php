<?php

declare(strict_types=1);

namespace App\Filament\Resources\JobOffers\Schemas;

use App\Enums\JobContractType;
use App\Enums\JobLevel;
use App\Enums\JobOfferStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class JobOfferForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make("Offre d'emploi")
                ->columns(2)
                ->schema([
                    TextInput::make('title')
                        ->label('Intitulé du poste')
                        ->required()
                        ->maxLength(200)
                        ->columnSpanFull(),

                    Select::make('contract_type')
                        ->label('Type de contrat')
                        ->options(JobContractType::options())
                        ->required(),

                    Select::make('level')
                        ->label('Niveau')
                        ->options(JobLevel::options())
                        ->required(),

                    Select::make('status')
                        ->label('Statut')
                        ->options(JobOfferStatus::options())
                        ->required(),

                    Toggle::make('is_remote')->label('Télétravail'),
                    Toggle::make('is_urgent')->label('Urgente'),

                    Textarea::make('description')
                        ->label('Description')
                        ->rows(10)
                        ->columnSpanFull(),

                    Textarea::make('rejection_reason')
                        ->label('Raison du refus')
                        ->rows(3)
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
