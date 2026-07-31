<?php

declare(strict_types=1);

namespace App\Filament\Resources\Events\GuestRegistrationResource\Pages;

use App\Filament\Resources\Events\GuestRegistrationResource;
use App\Models\GuestRegistration;
use App\Services\Events\TicketService;
use Filament\Actions\Action;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ViewGuestRegistration extends ViewRecord
{
    protected static string $resource = GuestRegistrationResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([

            Section::make('Participant')
                ->columns(2)
                ->schema([
                    ImageEntry::make('photo')
                        ->label('Photo')
                        ->disk('public')
                        ->circular()
                        ->imageHeight(80)
                        ->visible(fn (GuestRegistration $record): bool => $record->photo !== null),

                    TextEntry::make('full_name')
                        ->label('Nom complet')
                        ->state(fn (GuestRegistration $record): string => $record->fullName()),

                    TextEntry::make('email')
                        ->label('Email')
                        ->copyable()
                        ->icon('heroicon-o-envelope'),

                    TextEntry::make('whatsapp')
                        ->label('WhatsApp')
                        ->placeholder('-')
                        ->icon('heroicon-o-phone'),
                ]),

            Section::make('Inscription')
                ->columns(2)
                ->schema([
                    TextEntry::make('event.title')
                        ->label('Événement'),

                    TextEntry::make('status')
                        ->label('Statut')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'confirmed'  => 'success',
                            'waitlisted' => 'warning',
                            'cancelled'  => 'danger',
                            default      => 'gray',
                        })
                        ->formatStateUsing(fn (string $state): string => match ($state) {
                            'confirmed'  => 'Confirmé',
                            'waitlisted' => 'En attente',
                            'cancelled'  => 'Annulé',
                            default      => $state,
                        }),

                    TextEntry::make('registered_at')
                        ->label('Inscrit le')
                        ->dateTime('d/m/Y H:i'),

                    TextEntry::make('payment_status')
                        ->label('Paiement')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'paid'    => 'success',
                            'pending' => 'warning',
                            'free'    => 'gray',
                            default   => 'gray',
                        })
                        ->formatStateUsing(fn (string $state): string => match ($state) {
                            'paid'    => 'Payé',
                            'pending' => 'En attente',
                            'free'    => 'Gratuit',
                            default   => $state,
                        }),

                    TextEntry::make('amount_paid')
                        ->label('Montant payé')
                        ->placeholder('-')
                        ->formatStateUsing(fn (GuestRegistration $record): string => $record->amount_paid !== null
                            ? number_format((float) $record->amount_paid, 0, ',', ' ') . ' ' . ($record->event->currency ?? 'XOF')
                            : '-'),

                    TextEntry::make('promo_code_used')
                        ->label('Code promo utilisé')
                        ->placeholder('-')
                        ->fontFamily('mono'),
                ]),

            Section::make('Ticket')
                ->visible(fn (GuestRegistration $record): bool => $record->ticket_number !== null)
                ->columns(2)
                ->schema([
                    TextEntry::make('ticket_number')
                        ->label('Numéro de ticket')
                        ->fontFamily('mono')
                        ->copyable(),

                    TextEntry::make('ticket_qr_token')
                        ->label('URL de vérification')
                        ->state(fn (GuestRegistration $record): string => $record->ticket_qr_token !== null
                            ? route('events.ticket.verify', ['token' => $record->ticket_qr_token])
                            : '-')
                        ->copyable(),
                ]),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generate_ticket')
                ->label('Générer un ticket')
                ->icon('heroicon-o-ticket')
                ->color('warning')
                ->visible(fn (): bool => $this->record->ticket_number === null
                    && $this->record->status                          === 'confirmed')
                ->requiresConfirmation()
                ->action(function (): void {
                    /** @var GuestRegistration $guest */
                    $guest         = $this->record;
                    $ticketService = app(TicketService::class);

                    $guest->update([
                        'ticket_number'   => $ticketService->generateNumber($guest->event),
                        'ticket_qr_token' => $ticketService->generateQrToken(),
                    ]);

                    Notification::make()
                        ->title('Ticket généré avec succès')
                        ->success()
                        ->send();
                }),

            Action::make('cancel')
                ->label('Annuler l\'inscription')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn (): bool => $this->record->status !== 'cancelled')
                ->requiresConfirmation()
                ->action(function (): void {
                    $this->record->update([
                        'status'       => 'cancelled',
                        'cancelled_at' => now(),
                    ]);

                    if ($this->record->wasConfirmed ?? false) {
                        $this->record->event->decrement('registrations_count');
                    }

                    Notification::make()
                        ->title('Inscription annulée')
                        ->warning()
                        ->send();
                }),
        ];
    }
}
