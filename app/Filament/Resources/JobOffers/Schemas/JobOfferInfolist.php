<?php

declare(strict_types=1);

namespace App\Filament\Resources\JobOffers\Schemas;

use App\Enums\JobContractType;
use App\Enums\JobLevel;
use App\Enums\JobOfferStatus;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class JobOfferInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make("Détails de l'offre")
                ->columns(2)
                ->schema([
                    TextEntry::make('title')->label('Intitulé')->columnSpanFull(),
                    TextEntry::make('company.name')->label('Entreprise')->placeholder('-'),
                    TextEntry::make('poster.name')->label('Publié par')->placeholder('-'),
                    TextEntry::make('contract_type')->label('Contrat')
                        ->formatStateUsing(fn (JobContractType $state): string => $state->label()),
                    TextEntry::make('level')->label('Niveau')
                        ->formatStateUsing(fn (JobLevel $state): string => $state->label()),
                    TextEntry::make('status')->label('Statut')->badge()
                        ->color(fn (JobOfferStatus $state): string => $state->color())
                        ->formatStateUsing(fn (JobOfferStatus $state): string => $state->label()),
                    TextEntry::make('location')->label('Lieu')->placeholder('-'),
                    TextEntry::make('country')->label('Pays')->placeholder('-'),
                    IconEntry::make('is_remote')->label('Télétravail')->boolean(),
                    IconEntry::make('is_urgent')->label('Urgente')->boolean(),
                    TextEntry::make('views_count')->label('Vues'),
                    TextEntry::make('applications_count')->label('Candidatures'),
                    TextEntry::make('published_at')->label('Publiée le')->dateTime('d/m/Y H:i')->placeholder('-'),
                    TextEntry::make('expires_at')->label('Expire le')->dateTime('d/m/Y')->placeholder('-'),
                ]),

            Section::make('Description')
                ->schema([
                    TextEntry::make('description')->label('')->columnSpanFull()->prose(),
                ]),
        ]);
    }
}
