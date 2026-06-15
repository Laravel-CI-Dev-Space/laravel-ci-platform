<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\Jobs;

use App\Livewire\Jobs\JobApplicationForm;
use App\Models\JobApplication;
use App\Models\JobOffer;
use App\Models\User;
use Livewire\Livewire;

it('lets an authenticated user apply with a cover letter', function () {
    $user  = User::factory()->create();
    $offer = JobOffer::factory()->active()->create();

    Livewire::actingAs($user)
        ->test(JobApplicationForm::class, ['offer' => $offer])
        ->set('coverLetter', str_repeat('Je suis très motivé par ce poste. ', 3))
        ->call('submit')
        ->assertHasNoErrors()
        ->assertSet('submitted', true);

    $application = JobApplication::first();
    expect($application)->not->toBeNull();
    expect($application->user_id)->toBe($user->id);
    expect($application->job_offer_id)->toBe($offer->id);
});

it('requires a cover letter or a cv to submit', function () {
    $user  = User::factory()->create();
    $offer = JobOffer::factory()->active()->create();

    Livewire::actingAs($user)
        ->test(JobApplicationForm::class, ['offer' => $offer])
        ->call('submit')
        ->assertHasErrors(['cv']);

    expect(JobApplication::count())->toBe(0);
});

it('validates the cover letter minimum length', function () {
    $user  = User::factory()->create();
    $offer = JobOffer::factory()->active()->create();

    Livewire::actingAs($user)
        ->test(JobApplicationForm::class, ['offer' => $offer])
        ->set('coverLetter', 'Trop court')
        ->call('submit')
        ->assertHasErrors(['coverLetter' => 'min']);
});
