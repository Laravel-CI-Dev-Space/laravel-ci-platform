<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Jobs;

use App\Models\JobOffer;
use App\Models\User;
use App\Services\Jobs\JobApplicationService;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

uses(TestCase::class);

it('lets a user apply to a job offer', function () {
    $user  = User::factory()->create();
    $offer = JobOffer::factory()->active()->create(['applications_count' => 0]);
    $service = app(JobApplicationService::class);

    $application = $service->apply($user, $offer, [
        'cover_letter'  => 'Je suis très motivé par ce poste.',
        'portfolio_url' => 'https://example.com/portfolio',
    ]);

    expect($application->user_id)->toBe($user->id);
    expect($application->job_offer_id)->toBe($offer->id);
    expect($application->cover_letter)->toBe('Je suis très motivé par ce poste.');
    expect($offer->refresh()->applications_count)->toBe(1);
    expect($service->hasApplied($user, $offer))->toBeTrue();
});

it('rejects a duplicate application from the same user', function () {
    $user  = User::factory()->create();
    $offer = JobOffer::factory()->active()->create();
    $service = app(JobApplicationService::class);

    $service->apply($user, $offer, []);

    expect(fn () => $service->apply($user, $offer, []))
        ->toThrow(HttpException::class);
});

it('lists a user\'s applications with their job offers', function () {
    $user   = User::factory()->create();
    $offer1 = JobOffer::factory()->active()->create();
    $offer2 = JobOffer::factory()->active()->create();
    $service = app(JobApplicationService::class);

    $service->apply($user, $offer1, []);
    $service->apply($user, $offer2, []);

    $applications = $service->getUserApplications($user);

    expect($applications)->toHaveCount(2);
    expect($applications->pluck('job_offer_id')->sort()->values())
        ->toEqual(collect([$offer1->id, $offer2->id])->sort()->values());
});
