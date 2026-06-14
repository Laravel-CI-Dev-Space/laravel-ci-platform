<?php

declare(strict_types=1);

namespace App\Filament\Resources\Events\Pages;

use App\Enums\EventStatus;
use App\Filament\Resources\Events\EventResource;
use App\Models\Event;
use App\Services\Events\EventRecapService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditEvent extends EditRecord
{
    protected static string $resource = EventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),

            Action::make('publishRecap')
                ->label('Publier le récapitulatif')
                ->icon('heroicon-o-megaphone')
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn (Event $record): bool => $record->status === EventStatus::Completed && ! $record->hasRecap())
                ->action(function (Event $record, EventRecapService $recapService): void {
                    $recapService->publish(auth()->user(), $record);

                    Notification::make()
                        ->title('Récapitulatif publié')
                        ->success()
                        ->send();
                }),

            Action::make('unpublishRecap')
                ->label('Dépublier le récapitulatif')
                ->icon('heroicon-o-eye-slash')
                ->color('warning')
                ->requiresConfirmation()
                ->visible(fn (Event $record): bool => $record->hasRecap())
                ->action(function (Event $record, EventRecapService $recapService): void {
                    $recapService->unpublish($record);

                    Notification::make()
                        ->title('Récapitulatif dépublié')
                        ->success()
                        ->send();
                }),

            DeleteAction::make(),
        ];
    }
}
