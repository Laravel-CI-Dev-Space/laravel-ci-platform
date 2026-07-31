<?php

declare(strict_types=1);

namespace App\Filament\Resources\Events\Tables;

use App\Enums\EventStatus;
use App\Enums\EventType;
use App\Jobs\Events\SendEventReminder;
use App\Mail\NewsletterEventMail;
use App\Models\Event;
use App\Models\NewsletterSubscriber;
use Filament\Actions\Action as TableAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Mail;

class EventsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Titre')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->tooltip(fn (Event $record): string => $record->title),

                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (EventType $state): string => match ($state) {
                        EventType::Meetup     => 'warning',
                        EventType::Webinar    => 'info',
                        EventType::Hackathon  => 'purple',
                        EventType::Conference => 'success',
                        EventType::Workshop   => 'cyan',
                    })
                    ->formatStateUsing(fn (EventType $state): string => $state->label()),

                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (EventStatus $state): string => $state->color())
                    ->formatStateUsing(fn (EventStatus $state): string => $state->label()),

                TextColumn::make('starts_at')
                    ->label('Date de début')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('registrations_count')
                    ->label('Inscrits / Capacité')
                    ->formatStateUsing(fn (Event $record): string => $record->capacity !== null
                        ? "{$record->registrations_count} / {$record->capacity}"
                        : (string) $record->registrations_count)
                    ->sortable(),

                TextColumn::make('is_paid')
                    ->label('Accès')
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Payant' : 'Gratuit')
                    ->color(fn (bool $state): string => $state ? 'warning' : 'success'),

                TextColumn::make('price')
                    ->label('Prix')
                    ->placeholder('-')
                    ->formatStateUsing(fn (Event $record): string => $record->is_paid && $record->price !== null
                        ? number_format((float) $record->price, 0, ',', ' ') . ' ' . ($record->currency ?? 'XOF')
                        : '-')
                    ->sortable(),

                TextColumn::make('is_full')
                    ->label('Places')
                    ->badge()
                    ->state(fn (Event $record): string => match (true) {
                        $record->capacity === null => 'Illimité',
                        $record->isFull()          => 'Complet',
                        default                    => 'Disponibles',
                    })
                    ->color(fn (Event $record): string => match (true) {
                        $record->capacity === null => 'gray',
                        $record->isFull()          => 'danger',
                        default                    => 'success',
                    }),

                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])

            ->filters([
                SelectFilter::make('status')
                    ->label('Statut')
                    ->options(EventStatus::options()),

                SelectFilter::make('type')
                    ->label('Type')
                    ->options(EventType::options()),

                Filter::make('upcoming')
                    ->label('À venir uniquement')
                    ->query(fn (Builder $q) => $q->where('starts_at', '>', now())),

                Filter::make('past')
                    ->label('Passés uniquement')
                    ->query(fn (Builder $q) => $q->where('starts_at', '<=', now())),

                Filter::make('is_paid')
                    ->label('Payants uniquement')
                    ->query(fn (Builder $q) => $q->where('is_paid', true)),

                Filter::make('is_free')
                    ->label('Gratuits uniquement')
                    ->query(fn (Builder $q) => $q->where('is_paid', false)),

                Filter::make('with_waitlist')
                    ->label('Liste d\'attente activée')
                    ->query(fn (Builder $q) => $q->where('waitlist_enabled', true)),
            ])

            ->recordActions([
                ViewAction::make(),
                EditAction::make(),

                TableAction::make('publish')
                    ->label('Publier')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Event $record): bool => $record->status === EventStatus::Draft)
                    ->requiresConfirmation()
                    ->modalHeading('Publier cet événement ?')
                    ->modalDescription('L\'événement sera immédiatement visible par tous les membres.')
                    ->action(function (Event $record): void {
                        $record->update(['status' => 'published']);

                        Notification::make()
                            ->title('Événement publié avec succès')
                            ->success()
                            ->send();
                    }),

                TableAction::make('cancel_event')
                    ->label('Annuler l\'événement')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Event $record): bool => $record->status === EventStatus::Published)
                    ->schema([
                        Textarea::make('cancellation_reason')
                            ->label('Raison de l\'annulation')
                            ->required()
                            ->minLength(10)
                            ->rows(4),
                    ])
                    ->action(function (Event $record, array $data): void {
                        $record->update([
                            'status'              => 'cancelled',
                            'cancellation_reason' => $data['cancellation_reason'],
                        ]);

                        Notification::make()
                            ->title('Événement annulé')
                            ->warning()
                            ->send();
                    }),

                TableAction::make('send_reminder')
                    ->label('Envoyer rappel')
                    ->icon('heroicon-o-bell')
                    ->color('gray')
                    ->visible(fn (Event $record): bool => $record->status === EventStatus::Published && $record->isUpcoming())
                    ->requiresConfirmation()
                    ->modalHeading('Envoyer un rappel maintenant ?')
                    ->modalDescription('Un email de rappel sera envoyé à tous les inscrits confirmés.')
                    ->action(function (Event $record): void {
                        SendEventReminder::dispatch($record, 'manual');

                        Notification::make()
                            ->title('Rappel planifié dans la file')
                            ->success()
                            ->send();
                    }),

                TableAction::make('send_newsletter')
                    ->label('Envoyer la newsletter')
                    ->icon('heroicon-o-envelope')
                    ->color('info')
                    ->visible(fn (Event $record): bool => $record->status === EventStatus::Published && ! $record->newsletter_sent)
                    ->requiresConfirmation()
                    ->modalHeading('Envoyer la newsletter ?')
                    ->modalDescription(function (Event $record): string {
                        $count = NewsletterSubscriber::active()->count();
                        return "Cet événement sera envoyé à {$count} abonné(s) actif(s). Cette action est irréversible.";
                    })
                    ->action(function (Event $record): void {
                        $subscribers = NewsletterSubscriber::active()->get();

                        foreach ($subscribers as $subscriber) {
                            Mail::to($subscriber->email)->send(new NewsletterEventMail($record, $subscriber));
                        }

                        $record->update(['newsletter_sent' => true]);

                        Notification::make()
                            ->title("Newsletter envoyée à {$subscribers->count()} abonné(s)")
                            ->success()
                            ->send();
                    }),
            ])

            ->defaultSort('starts_at', 'asc');
    }
}
