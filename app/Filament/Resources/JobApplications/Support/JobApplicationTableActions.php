<?php

declare(strict_types=1);

namespace App\Filament\Resources\JobApplications\Support;

use App\Enums\Jobs\JobApplicationStatus;
use App\Filament\Support\FrenchActions;
use App\Models\JobApplication;
use App\Services\Jobs\JobApplicationService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

final class JobApplicationTableActions
{
    /**
     * @return array<int, Action>
     */
    public static function reviewActions(): array
    {
        return [
            FrenchActions::confirm(
                Action::make('accept')
                    ->label('Valider')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (JobApplication $record) => $record->status === JobApplicationStatus::PENDING),
                'Valider cette candidature ?',
                'La candidature sera marquée comme acceptée.',
            )
                ->action(function (JobApplication $record, JobApplicationService $service) {
                    $service->accept($record);
                    Notification::make()->title('Candidature acceptée')->success()->send();
                }),

            FrenchActions::confirm(
                Action::make('reject')
                    ->label('Refuser')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (JobApplication $record) => $record->status === JobApplicationStatus::PENDING),
                'Refuser cette candidature ?',
                'La candidature sera marquée comme refusée.',
            )
                ->action(function (JobApplication $record, JobApplicationService $service) {
                    $service->reject($record);
                    Notification::make()->title('Candidature refusée')->success()->send();
                }),
        ];
    }
}
