<?php

declare(strict_types=1);

namespace App\Filament\Company\Resources\CompanyJobOfferResource\Pages;

use App\Filament\Company\Resources\CompanyJobOfferResource;
use Filament\Resources\Pages\ListRecords;

class ListCompanyJobOffers extends ListRecords
{
    protected static string $resource = CompanyJobOfferResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
