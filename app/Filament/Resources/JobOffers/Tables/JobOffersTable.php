<?php

declare(strict_types=1);

namespace App\Filament\Resources\JobOffers\Tables;

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
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'cdi' => 'CDI', 'cdd' => 'CDD', 'freelance' => 'Freelance',
                        'internship' => 'Stage', 'apprenticeship' => 'Alternance', default => $state,
                    }),

                TextColumn::make('level')
                    ->label('Niveau')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'junior' => 'Junior', 'intermediate' => 'Intermédiaire',
                        'senior' => 'Senior', 'lead' => 'Lead', default => 'Tous',
                    }),

                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'active'   => 'success',
                        'pending'  => 'warning',
                        'expired'  => 'danger',
                        'filled'   => 'info',
                        'rejected' => 'danger',
                        default    => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'active'   => 'Active',
                        'pending'  => 'En attente',
                        'expired'  => 'Expirée',
                        'filled'   => 'Pourvue',
                        'rejected' => 'Refusée',
                        default    => ucfirst($state),
                    }),

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
                    ->options([
                        'active'   => 'Active',
                        'pending'  => 'En attente',
                        'expired'  => 'Expirée',
                        'filled'   => 'Pourvue',
                        'rejected' => 'Refusée',
                    ]),

                SelectFilter::make('contract_type')
                    ->label('Type de contrat')
                    ->options([
                        'cdi'            => 'CDI',
                        'cdd'            => 'CDD',
                        'freelance'      => 'Freelance',
                        'internship'     => 'Stage',
                        'apprenticeship' => 'Alternance',
                    ]),

                SelectFilter::make('level')
                    ->label('Niveau')
                    ->options([
                        'junior'       => 'Junior',
                        'intermediate' => 'Intermédiaire',
                        'senior'       => 'Senior',
                        'lead'         => 'Lead',
                    ]),

                Filter::make('pending')
                    ->label('En attente uniquement')
                    ->query(fn (Builder $q) => $q->where('status', 'pending')),
            ])

            ->actions([
                ViewAction::make(),
                EditAction::make(),

                TableAction::make('publish')
                    ->label('Publier')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (JobOffer $r): bool => $r->status === 'pending')
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
                    ->visible(fn (JobOffer $r): bool => $r->status === 'pending')
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
                    ->visible(fn (JobOffer $r): bool => $r->status === 'active')
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
