<?php

namespace App\Filament\Resources\Events\Tables;

use App\Enums\Events\EventStatus;
use App\Filament\Support\EnumFormatter;
use App\Filament\Support\FrenchActions;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class EventsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Créée le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('title')
                    ->label('Titre')
                    ->searchable()
                    ->sortable()
                    ->limit(40),

                TextColumn::make('type.name')
                    ->label('Type')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('start_date')
                    ->label('Début')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('location')
                    ->label('Lieu')
                    ->limit(25)
                    ->toggleable(),

                TextColumn::make('confirmed_registrations_count')
                    ->label('Inscrits')
                    ->alignCenter()
                    ->sortable(),

                TextColumn::make('waitlists_count')
                    ->label('Attente')
                    ->counts('waitlists')
                    ->alignCenter()
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->formatStateUsing(fn ($state) => EnumFormatter::label($state))
                    ->color(fn (EventStatus $state): string => match ($state) {
                        EventStatus::PUBLISHED => 'success',
                        EventStatus::DRAFT     => 'warning',
                        EventStatus::CANCELLED => 'danger',
                    }),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Statut')
                    ->options(EventStatus::options()),
            ])
            ->actions([
                FrenchActions::edit(),
                FrenchActions::delete(),
            ])
            ->defaultSort('start_date', 'desc');
    }
}
