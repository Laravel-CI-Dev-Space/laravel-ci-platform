<?php

declare(strict_types=1);

namespace App\Filament\Resources\Events\Pages;

use App\Filament\Resources\Events\EventResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditEvent extends EditRecord
{
    protected static string $resource = EventResource::class;

    // Pas de relation managers sur la page d'édition — ils sont sur la page détail
    public function getRelationManagers(): array
    {
        return [];
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
                ->label('Voir la page détail')
                ->icon('heroicon-o-eye'),

            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return EventResource::getUrl('view', ['record' => $this->record]);
    }
}
