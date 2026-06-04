<?php

declare(strict_types=1);

namespace App\Filament\Company\Resources\CompanyApplicationResource\Pages;

use App\Filament\Company\Resources\CompanyApplicationResource;
use Filament\Resources\Pages\ListRecords;

class ListCompanyApplications extends ListRecords
{
    protected static string $resource = CompanyApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
