<?php

declare(strict_types=1);

namespace App\Filament\Resources\Settings\Pages;

use App\Filament\Resources\Settings\AboutOriginSectionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAboutOriginSection extends CreateRecord
{
    protected static string $resource = AboutOriginSectionResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
