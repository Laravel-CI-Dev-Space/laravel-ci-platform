<?php

declare(strict_types=1);

namespace App\Filament\Resources\Companies\Pages;

use App\Filament\Resources\Companies\CompanyResource;
use App\Filament\Support\FrenchActions;
use App\Services\Jobs\CompanyService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\Width;

class EditCompany extends EditRecord
{
    protected static string $resource = CompanyResource::class;

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
            FrenchActions::confirm(
                Action::make('activate')
                    ->label('Activer')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn () => ! $this->record->isActive()),
                'Activer cette entreprise ?',
                'Elle pourra à nouveau être associée à de nouvelles offres.',
            )
                ->action(function (CompanyService $service) {
                    $service->activate($this->record);
                    $this->record->refresh();
                    Notification::make()->title('Entreprise activée')->success()->send();
                }),

            FrenchActions::confirm(
                Action::make('deactivate')
                    ->label('Désactiver')
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn () => $this->record->isActive()),
                'Désactiver cette entreprise ?',
                'Elle ne pourra plus être sélectionnée pour de nouvelles offres.',
            )
                ->action(function (CompanyService $service) {
                    $service->deactivate($this->record);
                    $this->record->refresh();
                    Notification::make()->title('Entreprise désactivée')->success()->send();
                }),
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
