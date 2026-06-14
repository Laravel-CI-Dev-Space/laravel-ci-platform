<?php

declare(strict_types=1);

namespace App\Observers;

use App\Enums\Jobs\JobOfferStatus;
use App\Models\JobOffer;
use App\Services\Jobs\JobAlertService;

final class JobOfferObserver
{
    public function __construct(
        private readonly JobAlertService $jobAlertService,
    ) {}

    public function created(JobOffer $offer): void
    {
        if ($offer->status === JobOfferStatus::ACTIVE) {
            $this->jobAlertService->notifyMatchingAlerts($offer);
        }
    }

    public function updated(JobOffer $offer): void
    {
        if ($offer->wasChanged('status') && $offer->status === JobOfferStatus::ACTIVE) {
            $this->jobAlertService->notifyMatchingAlerts($offer);
        }
    }
}
