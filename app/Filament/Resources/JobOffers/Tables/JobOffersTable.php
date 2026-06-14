<?php

declare(strict_types=1);

namespace App\Filament\Resources\JobOffers\Tables;

use App\Enums\JobContractType;
use App\Enums\JobLevel;
use App\Enums\JobOfferStatus;
use App\Models\JobOffer;
use Filament\Actions\Action as TableAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class JobOffersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Intitulé')
                    ->searchable()
                    ->limit(50)
                    ->tooltip(fn (JobOffer $r): string => $r->title),

                TextColumn::make('company.name')
                    ->label('Entreprise')
                    ->sortable()
                    ->searchable()
                    ->placeholder('—'),

                TextColumn::make('contract_type')
                    ->label('Contrat')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn (JobContractType $state): string => $state->label()),

                TextColumn::make('level')
                    ->label('Niveau')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn (JobLevel $state): string => $state->label()),

                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (JobOfferStatus $state): string => $state->color())
                    ->formatStateUsing(fn (JobOfferStatus $state): string => $state->label()),

                IconColumn::make('is_remote')->label('Remote')->boolean(),
                IconColumn::make('is_urgent')->label('Urgente')->boolean(),

                TextColumn::make('applications_count')
                    ->label('Candidatures')
                    ->sortable(),

                TextColumn::make('views_count')
                    ->label('Vues')
                    ->sortable(),

                TextColumn::make('expires_at')
                    ->label('Expire le')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->placeholder('—'),

                TextColumn::make('created_at')
                    ->label('Créée le')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])

            ->filters([
                SelectFilter::make('status')
                    ->label('Statut')
                    ->options(JobOfferStatus::options()),

                SelectFilter::make('contract_type')
                    ->label('Type de contrat')
                    ->options(JobContractType::options()),

                SelectFilter::make('level')
                    ->label('Niveau')
                    ->options(JobLevel::options()),

                Filter::make('pending')
                    ->label('En attente uniquement')
                    ->query(fn (Builder $q) => $q->where('status', 'pending')),

                Filter::make('remote')
                    ->label('Télétravail uniquement')
                    ->query(fn (Builder $q) => $q->where('is_remote', true)),

                Filter::make('urgent')
                    ->label('Offres urgentes')
                    ->query(fn (Builder $q) => $q->where('is_urgent', true)),

                Filter::make('expiring_soon')
                    ->label('Expirent dans 7 jours')
                    ->query(fn (Builder $q) => $q
                        ->where('status', 'active')
                        ->whereNotNull('expires_at')
                        ->where('expires_at', '<=', now()->addDays(7))
                        ->where('expires_at', '>', now())),
            ])

            ->actions([
                ViewAction::make(),
                EditAction::make(),

                TableAction::make('publish')
                    ->label('Publier')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (JobOffer $r): bool => $r->status === JobOfferStatus::Pending)
                    ->requiresConfirmation()
                    ->modalHeading("Publier cette offre d'emploi ?")
                    ->action(function (JobOffer $record): void {
                        $record->update([
                            'status'       => 'active',
                            'published_at' => now(),
                        ]);

                        Notification::make()->title('Offre publiée avec succès')->success()->send();
                    }),

                TableAction::make('reject')
                    ->label('Refuser')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (JobOffer $r): bool => $r->status === JobOfferStatus::Pending)
                    ->schema([
                        Textarea::make('rejection_reason')
                            ->label('Raison du refus')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (JobOffer $record, array $data): void {
                        $record->update([
                            'status'           => 'rejected',
                            'rejection_reason' => $data['rejection_reason'],
                        ]);

                        Notification::make()->title('Offre refusée')->warning()->send();
                    }),

                TableAction::make('filled')
                    ->label('Marquer comme pourvu')
                    ->icon('heroicon-o-user-plus')
                    ->color('info')
                    ->visible(fn (JobOffer $r): bool => $r->status === JobOfferStatus::Active)
                    ->requiresConfirmation()
                    ->action(fn (JobOffer $record) => $record->update(['status' => 'filled'])),

                TableAction::make('toggle_urgent')
                    ->label(fn (JobOffer $r): string => $r->is_urgent ? 'Retirer urgence' : 'Marquer urgent')
                    ->icon(fn (JobOffer $r): string => $r->is_urgent ? 'heroicon-o-x-mark' : 'heroicon-o-exclamation-triangle')
                    ->color(fn (JobOffer $r): string => $r->is_urgent ? 'gray' : 'warning')
                    ->action(fn (JobOffer $record) => $record->update(['is_urgent' => ! $record->is_urgent])),

                DeleteAction::make()->requiresConfirmation(),
            ])

            ->defaultSort('created_at', 'desc');
    }
}
