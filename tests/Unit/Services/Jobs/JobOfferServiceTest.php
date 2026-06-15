<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Jobs;

use App\Enums\JobOfferStatus;
use App\Models\Company;
use App\Models\JobOffer;
use App\Models\User;
use App\Services\Jobs\JobOfferService;
use Tests\TestCase;

uses(TestCase::class);

it('creates a pending job offer with a 30-day expiry', function () {
    $company = Company::factory()->create();
    $user    = User::factory()->create();
    $service = app(JobOfferService::class);

    $offer = $service->createOffer([
        'title'         => 'Développeur Laravel Senior',
        'description'   => 'Nous recherchons un développeur Laravel expérimenté.',
        'contract_type' => 'cdi',
        'level'         => 'senior',
        'location'      => 'Abidjan',
    ], $company, $user);

    expect($offer->status)->toBe(JobOfferStatus::Pending);
    expect($offer->company_id)->toBe($company->id);
    expect($offer->posted_by)->toBe($user->id);
    expect($offer->expires_at->isSameDay(now()->addDays(30)))->toBeTrue();
});

it('toggles a favorite job offer on and off', function () {
    $user    = User::factory()->create();
    $offer   = JobOffer::factory()->active()->create();
    $service = app(JobOfferService::class);

    $added = $service->toggleFavorite($user, $offer);
    expect($added)->toBeTrue();

    $removed = $service->toggleFavorite($user, $offer);
    expect($removed)->toBeFalse();
});

it('expires offers past their expiry date', function () {
    $expired = JobOffer::factory()->active()->create(['expires_at' => now()->subDay()]);
    $valid   = JobOffer::factory()->active()->create(['expires_at' => now()->addWeek()]);
    $service = app(JobOfferService::class);

    $count = $service->expireOldOffers();

    expect($count)->toBe(1);
    expect($expired->refresh()->status)->toBe(JobOfferStatus::Expired);
    expect($valid->refresh()->status)->toBe(JobOfferStatus::Active);
});

it('filters active offers by search term', function () {
    JobOffer::factory()->active()->create(['title' => 'Développeur Laravel Senior']);
    JobOffer::factory()->active()->create(['title' => 'Designer UI/UX']);
    $service = app(JobOfferService::class);

    $results = $service->getOffers(['search' => 'Laravel']);

    expect($results->total())->toBe(1);
    expect($results->first()->title)->toBe('Développeur Laravel Senior');
});
