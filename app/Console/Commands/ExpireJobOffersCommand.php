<?php

namespace App\Console\Commands;

use App\Enums\Jobs\JobOfferStatus;
use App\Models\JobOffer;
use Illuminate\Console\Command;

class ExpireJobOffersCommand extends Command
{
    protected $signature = 'job-offers:expire';

    protected $description = 'Marque comme expirées les offres actives publiées depuis plus de 30 jours';

    public function handle(): int
    {
        $count = JobOffer::query()
            ->where('status', JobOfferStatus::ACTIVE)
            ->where('created_at', '<', now()->subDays(30))
            ->update(['status' => JobOfferStatus::EXPIRED]);

        $this->info("{$count} offre(s) expirée(s).");

        return self::SUCCESS;
    }
}
