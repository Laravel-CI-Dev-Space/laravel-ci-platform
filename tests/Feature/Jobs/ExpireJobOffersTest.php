<?php

declare(strict_types=1);

use App\Enums\Jobs\JobOfferStatus;
use App\Models\JobOffer;

test('expire command marks offers older than 30 days as expired', function () {
    $old = JobOffer::factory()->active()->create([
        'created_at' => now()->subDays(31),
    ]);

    $recent = JobOffer::factory()->active()->create([
        'created_at' => now()->subDays(10),
    ]);

    $this->artisan('job-offers:expire')->assertSuccessful();

    expect($old->fresh()->status)->toBe(JobOfferStatus::EXPIRED);
    expect($recent->fresh()->status)->toBe(JobOfferStatus::ACTIVE);
});
