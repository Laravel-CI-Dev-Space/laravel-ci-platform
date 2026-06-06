<?php

declare(strict_types=1);

namespace App\Filament\Resources\Settings\Pages;

use App\Filament\Resources\Settings\HomeStatResource;
use Filament\Resources\Pages\CreateRecord;

class CreateHomeStat extends CreateRecord
{
    protected static string $resource = HomeStatResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
