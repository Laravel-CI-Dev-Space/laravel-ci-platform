<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs;

use App\Models\JobOffer;

it('renders the job board index', function () {
    JobOffer::factory()->active()->create(['title' => 'Développeur Laravel Senior']);

    $response = $this->get(route('jobs.index'));

    $response->assertOk();
});

it('shows an active job offer', function () {
    $offer = JobOffer::factory()->active()->create(['title' => 'Développeur Laravel Junior']);

    $response = $this->get(route('jobs.show', $offer->slug));

    $response->assertOk();
    $response->assertSee($offer->title);
});
