<?php

namespace App\Filament\Resources\EventWaitlists\Tables;

use App\Filament\Support\FrenchActions;
use App\Models\EventWaitlist;
use App\Services\Events\EventService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class EventWaitlistsTable
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

                TextColumn::make('position')
                    ->label('Position')
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('user.name')
                    ->label('Membre')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Ajouté le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('event_id')
                    ->label('Événement')
                    ->relationship('event', 'title')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                FrenchActions::confirm(
                    Action::make('promote')
                        ->label('Promouvoir')
                        ->icon('heroicon-o-arrow-up-circle')
                        ->color('success')
                        ->requiresConfirmation(),
                    'Promouvoir ce membre ?',
                    'Il sera inscrit à l\'événement et retiré de la liste d\'attente.',
                )
                    ->action(function (EventWaitlist $record, EventService $service) {
                        $service->promoteWaitlistEntry($record);
                        Notification::make()->title('Membre promu depuis la liste d\'attente')->success()->send();
                    }),

                FrenchActions::confirm(
                    Action::make('remove')
                        ->label('Retirer')
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->requiresConfirmation(),
                    'Retirer de la liste d\'attente ?',
                    'Le membre sera retiré sans être inscrit à l\'événement.',
                )
                    ->action(function (EventWaitlist $record, EventService $service) {
                        $service->removeFromWaitlist($record);
                        Notification::make()->title('Membre retiré de la liste d\'attente')->success()->send();
                    }),
            ])
            ->defaultSort('position');
    }
}
