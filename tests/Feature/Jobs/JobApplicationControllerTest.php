<?php

declare(strict_types=1);

use App\Models\JobApplication;
use App\Models\JobOffer;

it('submits an application for an active job offer', function () {
    $user  = makeMember();
    $offer = JobOffer::factory()->active()->create();

    $response = $this->actingAs($user)->post(route('jobs.applications.store', $offer), [
        'cover_letter' => str_repeat('Je suis très motivé pour ce poste. ', 5),
    ]);

    $response->assertRedirect(route('jobs.show', $offer->slug));
    $response->assertSessionHas('success');
    $this->assertDatabaseHas('job_applications', [
        'job_offer_id' => $offer->id,
        'user_id'      => $user->id,
    ]);
});

it('rejects an application for an offer that is not active', function () {
    $user  = makeMember();
    $offer = JobOffer::factory()->create(['status' => 'draft']);

    $response = $this->actingAs($user)->post(route('jobs.applications.store', $offer), [
        'cover_letter' => str_repeat('Je suis très motivé pour ce poste. ', 5),
    ]);

    $response->assertStatus(422);
});

it('rejects a duplicate application from the same member', function () {
    $user  = makeMember();
    $offer = JobOffer::factory()->active()->create();

    JobApplication::create([
        'job_offer_id' => $offer->id,
        'user_id'      => $user->id,
        'status'       => 'pending',
        'cover_letter' => 'Première candidature.',
    ]);

    $response = $this->actingAs($user)->post(route('jobs.applications.store', $offer), [
        'cover_letter' => str_repeat('Deuxième tentative. ', 5),
    ]);

    $response->assertStatus(422);
});
