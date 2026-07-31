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

    /**
     * Filament FileUpload retourne 'job-covers/filename.jpg' (path relatif au disk).
     * On normalise en stockant uniquement 'filename.jpg' - cohérent avec AssetService.
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (! empty($data['cover_image'])) {
            $data['cover_image'] = basename((string) $data['cover_image']);
        }

        if (! empty($data['attachment_path'])) {
            $data['attachment_path'] = basename((string) $data['attachment_path']);
        }

        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        $account = Auth::guard('company')->user();

        return app(JobOfferService::class)->createOffer(
            data:    $data,
            company: Company::find($account->company_id),
            user:    null,
        );
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Offre soumise pour validation - vous serez notifié après examen.';
    }
}
