<?php

namespace App\Filament\Resources\JobOffers\Tables;

use App\Enums\Jobs\JobOfferStatus;
use App\Enums\Jobs\JobOfferType;
use App\Filament\Support\EnumFormatter;
use App\Filament\Support\FrenchActions;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class JobOffersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Créée le')
                    ->dateTime('d/m/Y')
                    ->sortable(),
                TextColumn::make('title')
                    ->label('Titre')
                    ->searchable()
                    ->sortable()
                    ->limit(40),
                TextColumn::make('company.name')
                    ->label('Entreprise')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('company.email')
                    ->label('Email candidatures')
                    ->searchable()
                    ->toggleable()
                    ->placeholder('—'),

                TextColumn::make('location')
                    ->label('Lieu')
                    ->toggleable(),

                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => EnumFormatter::label($state)),

                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->formatStateUsing(fn ($state) => EnumFormatter::label($state))
                    ->color(fn (JobOfferStatus $state): string => match ($state) {
                        JobOfferStatus::ACTIVE  => 'success',
                        JobOfferStatus::DRAFT   => 'warning',
                        JobOfferStatus::EXPIRED => 'gray',
                    }),

                TextColumn::make('applications_count')
                    ->label('Candidatures')
                    ->counts('applications')
                    ->alignCenter()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Créée le')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Type')
                    ->options(JobOfferType::options()),

                SelectFilter::make('status')
                    ->label('Statut')
                    ->options(JobOfferStatus::options()),
            ])
            ->actions([
                FrenchActions::edit(),
                FrenchActions::delete(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
