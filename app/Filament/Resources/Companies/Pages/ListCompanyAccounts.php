<?php

declare(strict_types=1);

namespace App\Filament\Resources\Companies\Pages;

use App\Filament\Resources\Companies\CompanyAccountResource;
use Filament\Resources\Pages\ListRecords;

class ListCompanyAccounts extends ListRecords
{
    protected static string $resource = CompanyAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
