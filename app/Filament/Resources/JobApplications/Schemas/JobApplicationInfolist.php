<?php

declare(strict_types=1);

namespace App\Filament\Resources\JobApplications\Schemas;

use App\Enums\Jobs\JobApplicationStatus;
use App\Filament\Resources\JobOffers\JobOfferResource;
use App\Filament\Support\EnumFormatter;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class JobApplicationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Candidature')
                ->columns(2)
                ->schema([
                    TextEntry::make('status')
                        ->label('Statut')
                        ->badge()
                        ->formatStateUsing(fn ($state) => EnumFormatter::label($state))
                        ->color(fn (JobApplicationStatus $state): string => match ($state) {
                            JobApplicationStatus::PENDING  => 'warning',
                            JobApplicationStatus::ACCEPTED => 'success',
                            JobApplicationStatus::REJECTED => 'danger',
                        }),

                    TextEntry::make('created_at')
                        ->label('Reçue le')
                        ->dateTime('d/m/Y à H:i'),
                ]),

            Section::make('Candidat')
                ->columns(2)
                ->schema([
                    TextEntry::make('user.name')
                        ->label('Nom'),

                    TextEntry::make('user.email')
                        ->label('Email')
                        ->copyable(),

                    TextEntry::make('user.github_username')
                        ->label('GitHub')
                        ->prefix('@')
                        ->placeholder('—')
                        ->url(fn ($record) => $record->user?->githubUrl())
                        ->openUrlInNewTab(),
                ]),

            Section::make('Offre')
                ->columns(2)
                ->schema([
                    TextEntry::make('jobOffer.title')
                        ->label('Poste')
                        ->url(fn ($record) => JobOfferResource::getUrl('edit', ['record' => $record->jobOffer]))
                        ->openUrlInNewTab(),

                    TextEntry::make('jobOffer.company.name')
                        ->label('Entreprise'),

                    TextEntry::make('jobOffer.location')
                        ->label('Localisation')
                        ->placeholder('—'),

                    TextEntry::make('jobOffer.type')
                        ->label('Type de contrat')
                        ->formatStateUsing(fn ($state) => EnumFormatter::label($state)),

                    TextEntry::make('jobOffer.company.email')
                        ->label('Email entreprise')
                        ->placeholder('—')
                        ->copyable(),
                ]),

            Section::make('Lettre de motivation')
                ->schema([
                    TextEntry::make('cover_letter')
                        ->label('')
                        ->placeholder('Aucune lettre de motivation.')
                        ->formatStateUsing(fn (?string $state) => filled($state)
                            ? new HtmlString(nl2br(e($state)))
                            : null)
                        ->html()
                        ->columnSpanFull(),
                ]),

            Section::make('CV')
                ->visible(fn ($record) => filled($record->cv_path) || filled($record->user?->profile?->cv))
                ->schema([
                    TextEntry::make('cv_path')
                        ->label('Fichier candidature')
                        ->placeholder('Aucun CV joint à la candidature.')
                        ->formatStateUsing(fn (?string $state) => $state ? basename($state) : null),

                    TextEntry::make('user.id')
                        ->label('CV profil membre')
                        ->formatStateUsing(fn () => 'Télécharger')
                        ->url(fn ($record) => route('cv.download', ['userId' => $record->user_id]))
                        ->openUrlInNewTab()
                        ->visible(fn ($record) => $record->user?->profile?->cv !== null),
                ]),
        ]);
    }
}
