<?php

namespace App\Filament\Resources\EventRegistrations\Tables;

use App\Enums\Events\EventRegistrationStatus;
use App\Filament\Support\EnumFormatter;
use App\Filament\Support\FrenchActions;
use App\Models\EventRegistration;
use App\Services\Events\EventService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class EventRegistrationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('event.title')
                    ->label('Événement')
                    ->searchable()
                    ->sortable()
                    ->limit(35),

                TextColumn::make('user.name')
                    ->label('Membre')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->formatStateUsing(fn ($state) => EnumFormatter::label($state))
                    ->color(fn (EventRegistrationStatus $state): string => match ($state) {
                        EventRegistrationStatus::CONFIRMED => 'success',
                        EventRegistrationStatus::PENDING   => 'warning',
                        EventRegistrationStatus::CANCELLED => 'gray',
                    }),

                TextColumn::make('reminder_types')
                    ->label('Rappels')
                    ->formatStateUsing(fn (EventRegistration $record) => $record->reminderTypesLabel())
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Inscrit le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('event_id')
                    ->label('Événement')
                    ->relationship('event', 'title')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('status')
                    ->label('Statut')
                    ->options(EventRegistrationStatus::options()),
            ])
            ->actions([
                FrenchActions::confirm(
                    Action::make('cancel')
                        ->label('Annuler')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->visible(fn (EventRegistration $record) => $record->status === EventRegistrationStatus::CONFIRMED),
                    'Annuler cette inscription ?',
                    'Le membre sera désinscrit et la liste d\'attente pourra être promue.',
                )
                    ->action(function (EventRegistration $record, EventService $service) {
                        $service->cancelRegistrationAsAdmin($record);
                        Notification::make()->title('Inscription annulée')->success()->send();
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
