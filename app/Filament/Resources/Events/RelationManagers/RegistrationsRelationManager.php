<?php

declare(strict_types=1);

namespace App\Filament\Resources\Events\RelationManagers;

use App\Enums\EventRegistrationStatus;
use App\Models\EventRegistration;
use Filament\Actions\Action as TableAction;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class RegistrationsRelationManager extends RelationManager
{
    protected static string $relationship = 'registrations';

    protected static ?string $title = 'Inscriptions';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Membre')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable()
                    ->copyable(),

                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (EventRegistrationStatus $state): string => $state->color())
                    ->formatStateUsing(fn (EventRegistrationStatus $state): string => $state->label()),

                TextColumn::make('registered_at')
                    ->label('Inscrit le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                ToggleColumn::make('reminder_sent')
                    ->label('Rappel envoyé'),
            ])

            ->recordActions([
                TableAction::make('mark_attended')
                    ->label('Marquer présent')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->visible(fn (EventRegistration $record): bool => $record->status === EventRegistrationStatus::Confirmed)
                    ->requiresConfirmation()
                    ->action(function (EventRegistration $record): void {
                        $record->update(['status' => 'attended']);

                        Notification::make()
                            ->title('Membre marqué comme présent')
                            ->success()
                            ->send();
                    }),

                TableAction::make('cancel_registration')
                    ->label('Annuler')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->visible(fn (EventRegistration $record): bool => in_array($record->status, [EventRegistrationStatus::Confirmed, EventRegistrationStatus::Waitlisted], true))
                    ->requiresConfirmation()
                    ->action(function (EventRegistration $record): void {
                        $wasConfirmed = $record->isConfirmed();

                        $record->update([
                            'status'       => 'cancelled',
                            'cancelled_at' => now(),
                        ]);

                        if ($wasConfirmed) {
                            $record->event->decrement('registrations_count');
                        }

                        Notification::make()
                            ->title('Inscription annulée')
                            ->warning()
                            ->send();
                    }),
            ])

            ->defaultSort('registered_at', 'asc');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }
}
