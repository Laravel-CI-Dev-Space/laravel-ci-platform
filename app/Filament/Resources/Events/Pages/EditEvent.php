<?php

namespace App\Filament\Resources\Events\Pages;

use App\Enums\Events\EventStatus;
use App\Filament\Resources\EventRegistrations\EventRegistrationResource;
use App\Filament\Resources\Events\EventResource;
use App\Filament\Resources\EventWaitlists\EventWaitlistResource;
use App\Filament\Support\FrenchActions;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditEvent extends EditRecord
{
    protected static string $resource = EventResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('registrations')
                ->label('Inscrits')
                ->icon('heroicon-o-user-group')
                ->url(fn () => EventRegistrationResource::getUrl('index', [
                    'tableFilters' => [
                        'event_id' => ['value' => $this->record->id],
                        'status'   => ['value' => 'confirmed'],
                    ],
                ])),

            Action::make('waitlist')
                ->label('Liste d\'attente')
                ->icon('heroicon-o-queue-list')
                ->url(fn () => EventWaitlistResource::getUrl('index', [
                    'tableFilters' => [
                        'event_id' => ['value' => $this->record->id],
                    ],
                ])),

            FrenchActions::confirm(
                Action::make('publish')
                    ->label('Publier')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn () => $this->record->status === EventStatus::DRAFT),
                'Publier cet événement ?',
                'L\'événement sera visible et ouvert aux inscriptions.',
            )
                ->action(function () {
                    $this->record->update(['status' => EventStatus::PUBLISHED]);
                    $this->record->refresh();
                    Notification::make()->title('Événement publié')->success()->send();
                }),

            FrenchActions::confirm(
                Action::make('cancelEvent')
                    ->label('Annuler l\'événement')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn () => $this->record->status === EventStatus::PUBLISHED),
                'Annuler cet événement ?',
                'L\'événement ne sera plus ouvert aux inscriptions.',
            )
                ->action(function () {
                    $this->record->update(['status' => EventStatus::CANCELLED]);
                    $this->record->refresh();
                    Notification::make()->title('Événement annulé')->success()->send();
                }),

            FrenchActions::delete(),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction()->label('Enregistrer'),
            $this->getCancelFormAction()->label('Annuler'),
        ];
    }
}
