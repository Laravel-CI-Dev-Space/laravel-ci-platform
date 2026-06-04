<?php

declare(strict_types=1);

namespace App\Filament\Resources\Companies\Pages;

use App\Filament\Resources\Companies\CompanyRegistrationResource;
use Filament\Resources\Pages\ListRecords;

class ListCompanyRegistrations extends ListRecords
{
    protected static string $resource = CompanyRegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
