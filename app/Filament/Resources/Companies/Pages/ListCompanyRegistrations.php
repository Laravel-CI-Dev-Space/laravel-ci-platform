<?php

declare(strict_types=1);

namespace App\Filament\Resources\Companies\Pages;

use App\Filament\Resources\Companies\CompanyRegistrationResource;
use App\Filament\Widgets\CompanyStatsWidget;
use Filament\Resources\Pages\ListRecords;

class ListCompanyRegistrations extends ListRecords
{
    protected static string $resource = CompanyRegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getHeaderWidgets(): array
    {
        return [CompanyStatsWidget::class];
    }

    public function getHeaderWidgetsColumns(): int|array
    {
        return 5;
    }
}
