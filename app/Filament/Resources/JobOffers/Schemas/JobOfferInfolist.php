<?php

declare(strict_types=1);

namespace App\Filament\Resources\JobOffers\Schemas;

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
                    TextEntry::make('company.name')->label('Entreprise')->placeholder('—'),
                    TextEntry::make('poster.name')->label('Publié par')->placeholder('—'),
                    TextEntry::make('contract_type')->label('Contrat')
                        ->formatStateUsing(fn ($state) => match ($state) {
                            'cdi' => 'CDI', 'cdd' => 'CDD', 'freelance' => 'Freelance',
                            'internship' => 'Stage', 'apprenticeship' => 'Alternance', default => $state,
                        }),
                    TextEntry::make('level')->label('Niveau')
                        ->formatStateUsing(fn ($state) => match ($state) {
                            'junior' => 'Junior', 'intermediate' => 'Intermédiaire',
                            'senior' => 'Senior', 'lead' => 'Lead', default => 'Tous niveaux',
                        }),
                    TextEntry::make('status')->label('Statut')->badge()
                        ->color(fn ($state) => match ($state) {
                            'active' => 'success', 'pending' => 'warning', 'expired' => 'danger',
                            'filled' => 'info', 'rejected' => 'danger', default => 'gray',
                        }),
                    TextEntry::make('location')->label('Lieu')->placeholder('—'),
                    TextEntry::make('country')->label('Pays')->placeholder('—'),
                    IconEntry::make('is_remote')->label('Télétravail')->boolean(),
                    IconEntry::make('is_urgent')->label('Urgente')->boolean(),
                    TextEntry::make('views_count')->label('Vues'),
                    TextEntry::make('applications_count')->label('Candidatures'),
                    TextEntry::make('published_at')->label('Publiée le')->dateTime('d/m/Y H:i')->placeholder('—'),
                    TextEntry::make('expires_at')->label('Expire le')->dateTime('d/m/Y')->placeholder('—'),
                ]),

            Section::make('Description')
                ->schema([
                    TextEntry::make('description')->label('')->columnSpanFull()->prose(),
                ]),
        ]);
    }
}
