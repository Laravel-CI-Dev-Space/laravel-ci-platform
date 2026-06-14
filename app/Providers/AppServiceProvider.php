<?php

namespace App\Providers;

use App\Models\JobOffer;
use App\Observers\JobOfferObserver;
use BladeUI\Icons\Factory;
use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->callAfterResolving(Factory::class, function (Factory $factory): void {
            $factory->add('social', [
                'path'   => resource_path('svg/social'),
                'prefix' => 'social',
            ]);
        });
    }

    public function boot(): void
    {
        Carbon::setLocale(config('app.locale'));

        JobOffer::observe(JobOfferObserver::class);
    }
}
