<?php

declare(strict_types=1);

namespace App\Filament\Company\Resources\CompanyApplicationResource\Pages;

use App\Filament\Company\Resources\CompanyApplicationResource;
use Filament\Resources\Pages\ViewRecord;

class ViewCompanyApplication extends ViewRecord
{
    protected static string $resource = CompanyApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
