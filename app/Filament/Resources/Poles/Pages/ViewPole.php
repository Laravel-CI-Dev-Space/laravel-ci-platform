<?php

namespace App\Filament\Resources\Poles\Pages;

use App\Filament\Resources\Poles\PoleResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPole extends ViewRecord
{
    protected static string $resource = PoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make(),
        ];
    }
}
