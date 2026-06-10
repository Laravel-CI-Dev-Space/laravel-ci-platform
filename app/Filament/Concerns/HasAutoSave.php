<?php

namespace App\Filament\Concerns;

use App\Models\VitrineSetting;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

trait HasAutoSave
{
    public bool $autoSave = false;

    public function toggleAutoSave(): void
    {
        $this->autoSave = ! $this->autoSave;
    }

    protected function autoSaveAction(): Action
    {
        return Action::make('toggleAutoSave')
            ->label('Auto save')
            ->icon(fn () => $this->autoSave ? 'heroicon-o-bolt' : 'heroicon-o-bolt-slash')
            ->color(fn () => $this->autoSave ? 'success' : 'gray')
            ->action(fn () => $this->toggleAutoSave());
    }

    protected function clearCacheAction(): Action
    {
        return Action::make('clearCache')
            ->label('Vider le cache')
            ->icon('heroicon-o-arrow-path')
            ->color('warning')
            ->action(function () {
                VitrineSetting::forgetAll();
                Notification::make()
                    ->title('Cache vidé')
                    ->body('Les données seront rechargées depuis la base de données.')
                    ->success()
                    ->send();
            });
    }
}
