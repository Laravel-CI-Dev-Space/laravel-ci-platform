<?php

declare(strict_types=1);

namespace App\Services\Jobs;

use App\Actions\Jobs\AcceptJobApplicationAction;
use App\Actions\Jobs\RejectJobApplicationAction;
use App\Enums\Jobs\JobOfferStatus;
use App\Models\JobApplication;
use App\Models\JobOffer;
use Illuminate\Validation\ValidationException;

final class JobApplicationService
{
    public function __construct(
        private readonly AcceptJobApplicationAction $acceptJobApplication,
        private readonly RejectJobApplicationAction $rejectJobApplication,
    ) {}

    public function accept(JobApplication $application): JobApplication
    {
        return $this->acceptJobApplication->execute($application);
    }

    public function reject(JobApplication $application): JobApplication
    {
        return $this->rejectJobApplication->execute($application);
    }

    public function publishOffer(JobOffer $offer): JobOffer
    {
        if ($offer->status !== JobOfferStatus::DRAFT) {
            throw ValidationException::withMessages([
                'status' => 'Seules les offres en brouillon peuvent être publiées.',
            ]);
        }

        $offer->update(['status' => JobOfferStatus::ACTIVE]);

        return $offer->fresh();
    }

    public function deactivateOffer(JobOffer $offer): JobOffer
    {
        if ($offer->status !== JobOfferStatus::ACTIVE) {
            throw ValidationException::withMessages([
                'status' => 'Seules les offres actives peuvent être désactivées.',
            ]);
        }

        $offer->update(['status' => JobOfferStatus::EXPIRED]);

        return $offer->fresh();
    }
}
