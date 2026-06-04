<?php

use App\Actions\Jobs\ExpireOldOffers;
use App\Services\Jobs\JobOfferService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/* ── Expire les offres d'emploi périmées (quotidien, minuit) ── */
Schedule::call(fn () => app(ExpireOldOffers::class)->handle(app(JobOfferService::class)))
    ->daily()
    ->name('expire-old-job-offers')
    ->withoutOverlapping();
