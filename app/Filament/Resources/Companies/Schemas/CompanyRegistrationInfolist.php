<?php

declare(strict_types=1);

namespace App\Filament\Resources\Companies\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CompanyRegistrationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Responsable')
                ->columns(2)
                ->schema([
                    TextEntry::make('first_name')->label('Prénom'),
                    TextEntry::make('last_name')->label('Nom'),
                    TextEntry::make('position')->label('Poste'),
                    TextEntry::make('phone')->label('Téléphone')->placeholder('—'),
                    TextEntry::make('email')->label('Email'),
                ]),

            Section::make("Informations de l'entreprise")
                ->columns(2)
                ->schema([
                    TextEntry::make('company_name')->label('Entreprise'),
                    TextEntry::make('business_domain')->label("Domaine d'activité"),
                    TextEntry::make('country')->label('Pays')->placeholder('—'),
                    TextEntry::make('city')->label('Ville')->placeholder('—'),
                    TextEntry::make('website')->label('Site web')->placeholder('—'),
                ]),

            Section::make('Message de présentation')
                ->schema([
                    TextEntry::make('motivation')->label('')->placeholder('Aucun message.')->columnSpanFull(),
                ]),

            Section::make('Décision')
                ->columns(2)
                ->schema([
                    TextEntry::make('status')
                        ->label('Statut')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'pending'  => 'warning',
                            'approved' => 'success',
                            'rejected' => 'danger',
                            default    => 'gray',
                        })
                        ->formatStateUsing(fn (string $state): string => match ($state) {
                            'pending'  => 'En attente',
                            'approved' => 'Approuvée',
                            'rejected' => 'Refusée',
                            default    => $state,
                        }),
                    TextEntry::make('reviewer.name')->label('Révisé par')->placeholder('—'),
                    TextEntry::make('reviewed_at')->label('Révisé le')->dateTime('d/m/Y H:i')->placeholder('—'),
                    TextEntry::make('rejection_reason')->label('Raison du refus')->placeholder('—')->columnSpanFull(),
                ]),
        ]);
    }
}
