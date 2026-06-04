<?php

declare(strict_types=1);

namespace App\Actions\Jobs;

use App\Services\Jobs\JobOfferService;
use Illuminate\Support\Facades\Log;

class ExpireOldOffers
{
    /**
     * Expire les offres d'emploi dont la date d'expiration est dépassée.
     * Exécutée quotidiennement par le scheduler.
     */
    public function handle(JobOfferService $service): void
    {
        $count = $service->expireOldOffers();

        Log::info("ExpireOldOffers: {$count} offre(s) expirée(s).");
    }
}
