<?php

namespace App\Filament\Resources\Poles\Pages;

use App\Filament\Resources\Poles\PoleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPoles extends ListRecords
{
    protected static string $resource = PoleResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
