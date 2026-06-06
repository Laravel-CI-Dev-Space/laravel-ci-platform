<?php

declare(strict_types=1);

namespace App\Filament\Resources\Settings\Pages;

use App\Filament\Resources\Settings\CommunityValueResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCommunityValue extends CreateRecord
{
    protected static string $resource = CommunityValueResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
