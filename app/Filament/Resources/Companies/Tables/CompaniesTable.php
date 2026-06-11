<?php

declare(strict_types=1);

namespace App\Filament\Resources\Companies\Tables;

use App\Filament\Support\FrenchActions;
use App\Models\Company;
use App\Services\Jobs\CompanyService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class CompaniesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('Email candidatures')
                    ->searchable()
                    ->placeholder('—'),

                TextColumn::make('website')
                    ->label('Site web')
                    ->url(fn (Company $record) => $record->website)
                    ->openUrlInNewTab()
                    ->limit(30)
                    ->placeholder('—')
                    ->toggleable(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                TextColumn::make('job_offers_count')
                    ->label('Offres')
                    ->counts('jobOffers')
                    ->alignCenter()
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Statut')
                    ->placeholder('Toutes')
                    ->trueLabel('Actives')
                    ->falseLabel('Inactives'),
            ])
            ->actions([
                FrenchActions::edit(),

                FrenchActions::confirm(
                    Action::make('activate')
                        ->label('Activer')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->visible(fn (Company $record) => ! $record->isActive()),
                    'Activer cette entreprise ?',
                    'Elle pourra à nouveau être associée à de nouvelles offres.',
                )
                    ->action(function (Company $record, CompanyService $service) {
                        $service->activate($record);
                        Notification::make()->title('Entreprise activée')->success()->send();
                    }),

                FrenchActions::confirm(
                    Action::make('deactivate')
                        ->label('Désactiver')
                        ->icon('heroicon-o-no-symbol')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->visible(fn (Company $record) => $record->isActive()),
                    'Désactiver cette entreprise ?',
                    'Elle ne pourra plus être sélectionnée pour de nouvelles offres.',
                )
                    ->action(function (Company $record, CompanyService $service) {
                        $service->deactivate($record);
                        Notification::make()->title('Entreprise désactivée')->success()->send();
                    }),
            ])
            ->defaultSort('name');
    }
}
