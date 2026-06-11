<?php

declare(strict_types=1);

namespace App\Services\Jobs;

use App\Models\Company;

final class CompanyService
{
    public function activate(Company $company): Company
    {
        $company->update(['is_active' => true]);

        return $company->fresh();
    }

    public function deactivate(Company $company): Company
    {
        $company->update(['is_active' => false]);

        return $company->fresh();
    }
}
