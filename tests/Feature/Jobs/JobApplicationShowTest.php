<?php

declare(strict_types=1);

use App\Models\JobApplication;
use App\Models\JobOffer;
use App\Models\Profile;
use App\Models\User;
use Database\Seeders\RoleSeeder;

beforeEach(function () {
    $this->seed(RoleSeeder::class);
});

function memberWithProfile(): User
{
    $user = User::factory()->membreActif()->create();
    Profile::create(['user_id' => $user->id]);

    return $user;
}

test('member can view their job application details', function () {
    $user = memberWithProfile();
    $offer = JobOffer::factory()->active()->create();
    $application = JobApplication::factory()->create([
        'job_offer_id' => $offer->id,
        'user_id'      => $user->id,
        'cover_letter' => 'Je suis très motivé pour ce poste.',
    ]);

    $response = $this->actingAs($user)->get(route('dashboard.member.applications.show', $application));

    $response->assertOk();
    $response->assertSee($offer->title);
    $response->assertSee('Je suis très motivé pour ce poste.');
    $response->assertSee('En attente');
});

test('member cannot view another users job application', function () {
    $owner = memberWithProfile();
    $other = memberWithProfile();
    $application = JobApplication::factory()->create([
        'user_id' => $owner->id,
    ]);

    $this->actingAs($other)
        ->get(route('dashboard.member.applications.show', $application))
        ->assertForbidden();
});

test('guest cannot view job application details', function () {
    $application = JobApplication::factory()->create();

    $this->get(route('dashboard.member.applications.show', $application))
        ->assertRedirect(route('login'));
});
