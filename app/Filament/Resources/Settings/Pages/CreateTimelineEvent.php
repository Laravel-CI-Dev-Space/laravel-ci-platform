<?php

declare(strict_types=1);

namespace App\Filament\Resources\Settings\Pages;

use App\Filament\Resources\Settings\TimelineEventResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTimelineEvent extends CreateRecord
{
    protected static string $resource = TimelineEventResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
