<?php

declare(strict_types=1);

namespace App\Filament\Resources\JobOffers\Pages;

use App\Filament\Resources\JobOffers\JobOfferResource;
use App\Filament\Widgets\JobOfferStatsWidget;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListJobOffers extends ListRecords
{
    protected static string $resource = JobOfferResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Publier une offre'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [JobOfferStatsWidget::class];
    }

    public function getHeaderWidgetsColumns(): int|array
    {
        return 5;
    }
}
