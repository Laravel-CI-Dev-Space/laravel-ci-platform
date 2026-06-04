<?php

declare(strict_types=1);

namespace App\Filament\Company\Resources\CompanyJobOfferResource\Pages;

use App\Filament\Company\Resources\CompanyJobOfferResource;
use App\Models\Company;
use App\Services\Jobs\JobOfferService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CreateCompanyJobOffer extends CreateRecord
{
    protected static string $resource = CompanyJobOfferResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function handleRecordCreation(array $data): Model
    {
        $account = Auth::guard('company')->user();

        return app(JobOfferService::class)->createOffer(
            data:    $data,
            company: Company::find($account->company_id),
            user:    null,
            // Filament FileUpload stocke déjà le fichier sur le disk 'public'
            // et retourne le path relatif dans $data — pas de re-upload nécessaire.
        );
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Offre soumise pour validation — vous serez notifié après examen.';
    }
}
