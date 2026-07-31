<?php

namespace App\Filament\Resources\Poles\Pages;

use App\Filament\Resources\Poles\PoleResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPole extends EditRecord
{
    protected static string $resource = PoleResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
