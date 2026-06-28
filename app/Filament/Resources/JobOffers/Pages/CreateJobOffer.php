<?php

declare(strict_types=1);

namespace App\Filament\Resources\JobOffers\Pages;

use App\Filament\Resources\JobOffers\JobOfferResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateJobOffer extends CreateRecord
{
    protected static string $resource = JobOfferResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['posted_by']    = auth()->id();
        $data['slug']         = Str::slug($data['title']) . '-' . Str::random(6);
        $data['published_at'] = $data['published_at'] ?? now();

        if (empty($data['status'])) {
            $data['status'] = 'active';
        }

        return $data;
    }
}
