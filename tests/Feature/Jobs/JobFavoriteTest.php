<?php

declare(strict_types=1);

use App\Livewire\Dashboard\MemberJobFavorites;
use App\Livewire\JobBoard\JobFavoriteToggle;
use App\Models\JobFavorite;
use App\Models\JobOffer;
use App\Models\User;
use App\Services\Jobs\JobFavoriteService;
use Database\Seeders\RoleSeeder;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(RoleSeeder::class);
});

test('active member can save a job offer', function () {
    $user  = User::factory()->membreActif()->create();
    $offer = JobOffer::factory()->active()->create(['title' => 'Offre à sauvegarder']);

    Livewire::actingAs($user)
        ->test(JobFavoriteToggle::class, ['jobOfferId' => $offer->id])
        ->call('toggle')
        ->assertSet('isFavorited', true)
        ->assertDispatched('app-toast', message: 'Offre ajoutée à vos favoris', type: 'success');

    $this->assertDatabaseHas('job_favorites', [
        'user_id'      => $user->id,
        'job_offer_id' => $offer->id,
    ]);
});

test('active member can remove a saved job offer', function () {
    $user  = User::factory()->membreActif()->create();
    $offer = JobOffer::factory()->active()->create();

    JobFavorite::create([
        'user_id'      => $user->id,
        'job_offer_id' => $offer->id,
    ]);

    Livewire::actingAs($user)
        ->test(JobFavoriteToggle::class, ['jobOfferId' => $offer->id])
        ->assertSet('isFavorited', true)
        ->call('toggle')
        ->assertSet('isFavorited', false);

    $this->assertDatabaseMissing('job_favorites', [
        'user_id'      => $user->id,
        'job_offer_id' => $offer->id,
    ]);
});

test('guest is redirected to login when toggling favorite', function () {
    $offer = JobOffer::factory()->active()->create();

    Livewire::test(JobFavoriteToggle::class, ['jobOfferId' => $offer->id])
        ->call('toggle')
        ->assertRedirect(route('login'));
});

test('member dashboard favorites component lists saved offers', function () {
    $user  = User::factory()->membreActif()->create();
    $offer = JobOffer::factory()->active()->create(['title' => 'Laravel Developer CI']);

    JobFavorite::create([
        'user_id'      => $user->id,
        'job_offer_id' => $offer->id,
    ]);

    Livewire::actingAs($user)
        ->test(MemberJobFavorites::class)
        ->assertSee('Laravel Developer CI');
});

test('member can remove a favorite from dashboard list', function () {
    $user  = User::factory()->membreActif()->create();
    $offer = JobOffer::factory()->active()->create(['title' => 'Offre à retirer']);

    JobFavorite::create([
        'user_id'      => $user->id,
        'job_offer_id' => $offer->id,
    ]);

    Livewire::actingAs($user)
        ->test(MemberJobFavorites::class)
        ->assertSee('Offre à retirer')
        ->call('openRemoveModal', $offer->id)
        ->assertSet('jobOfferIdToRemove', $offer->id)
        ->call('confirmRemove')
        ->assertSet('jobOfferIdToRemove', null)
        ->assertDontSee('Offre à retirer')
        ->assertDispatched('app-toast', message: 'Offre retirée de vos favoris', type: 'info');

    $this->assertDatabaseMissing('job_favorites', [
        'user_id'      => $user->id,
        'job_offer_id' => $offer->id,
    ]);
});
