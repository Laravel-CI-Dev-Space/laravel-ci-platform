<?php

namespace App\Filament\Resources\JobOffers\Pages;

use App\Enums\Jobs\JobOfferStatus;
use App\Filament\Resources\JobApplications\JobApplicationResource;
use App\Filament\Resources\JobOffers\JobOfferResource;
use App\Filament\Support\FrenchActions;
use App\Services\Jobs\JobApplicationService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\Width;

class EditJobOffer extends EditRecord
{
    protected static string $resource = JobOfferResource::class;

    public function getMaxContentWidth(): Width|string|null
    {
        return Width::Full;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('applications')
                ->label('Candidatures')
                ->icon('heroicon-o-user-group')
                ->url(fn () => JobApplicationResource::getUrl('index', [
                    'tableFilters' => [
                        'job_offer_id' => ['value' => $this->record->id],
                    ],
                ])),

            FrenchActions::confirm(
                Action::make('publish')
                    ->label('Publier')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn () => $this->record->status === JobOfferStatus::DRAFT),
                'Publier cette offre ?',
                'L\'offre sera visible sur le job board.',
            )
                ->action(function (JobApplicationService $service) {
                    $service->publishOffer($this->record);
                    $this->record->refresh();
                    Notification::make()->title('Offre publiée')->success()->send();
                }),

            FrenchActions::confirm(
                Action::make('deactivate')
                    ->label('Désactiver')
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn () => $this->record->status === JobOfferStatus::ACTIVE),
                'Désactiver cette offre ?',
                'L\'offre ne sera plus visible ni ouverte aux candidatures.',
            )
                ->action(function (JobApplicationService $service) {
                    $service->deactivateOffer($this->record);
                    $this->record->refresh();
                    Notification::make()->title('Offre désactivée')->success()->send();
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
