<?php

declare(strict_types=1);

use App\Enums\Jobs\JobApplicationStatus;
use App\Models\JobApplication;
use App\Models\JobOffer;
use App\Models\User;
use Database\Seeders\RoleSeeder;

beforeEach(function () {
    $this->seed(RoleSeeder::class);
});

test('guest cannot apply to a job offer', function () {
    $offer = JobOffer::factory()->active()->create();

    $this->post(route('jobs.apply', $offer))->assertRedirect(route('login'));
});

test('active member can apply to an open job offer', function () {
    $offer = JobOffer::factory()->active()->create();
    $user = User::factory()->membreActif()->create();

    $response = $this->actingAs($user)->post(route('jobs.apply', $offer), [
        'cover_letter' => 'Je suis très motivé.',
    ]);

    $response->assertRedirect(route('jobs.show', $offer));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('job_applications', [
        'job_offer_id' => $offer->id,
        'user_id'      => $user->id,
        'status'       => JobApplicationStatus::PENDING->value,
    ]);
});

test('member cannot apply twice to the same offer', function () {
    $offer = JobOffer::factory()->active()->create();
    $user = User::factory()->membreActif()->create();

    JobApplication::create([
        'job_offer_id' => $offer->id,
        'user_id'      => $user->id,
        'status'       => JobApplicationStatus::PENDING,
    ]);

    $this->actingAs($user)->post(route('jobs.apply', $offer))->assertForbidden();
});

test('member can submit a job offer as draft', function () {
    $user = User::factory()->membreActif()->create();

    $response = $this->actingAs($user)->post(route('jobs.store'), [
        'company_name'        => 'Tech CI',
        'company_description' => 'Startup locale',
        'title'               => 'Ingénieur full stack',
        'description'         => str_repeat('Description détaillée de l\'offre. ', 5),
        'location'            => 'Abidjan',
        'type'                => 'cdi',
    ]);

    $response->assertRedirect(route('jobs.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('job_offers', [
        'title'  => 'Ingénieur full stack',
        'status' => 'draft',
    ]);
});

test('guest cannot access job submission form', function () {
    $this->get(route('jobs.create'))->assertRedirect(route('login'));
});
