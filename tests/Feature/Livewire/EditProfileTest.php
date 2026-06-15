<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Livewire\EditProfile;
use App\Models\Profile;
use App\Models\User;
use Livewire\Livewire;

it('creates a profile for a first-time user on save', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(EditProfile::class)
        ->assertSet('isFirstTime', true)
        ->set('country', 'Côte d\'Ivoire')
        ->set('city', 'Abidjan')
        ->set('bio', 'Développeur Laravel passionné.')
        ->set('laravel_level', 'intermediaire')
        ->call('save')
        ->assertHasNoErrors();

    $profile = Profile::where('user_id', $user->id)->first();
    expect($profile)->not->toBeNull();
    expect($profile->country)->toBe('Côte d\'Ivoire');
    expect($profile->city)->toBe('Abidjan');
    expect($profile->bio)->toBe('Développeur Laravel passionné.');
});

it('loads existing profile data on mount', function () {
    $user = User::factory()->create();
    Profile::create([
        'user_id' => $user->id,
        'country' => 'France',
        'city'    => 'Paris',
        'bio'     => 'Profil existant',
    ]);

    Livewire::actingAs($user)
        ->test(EditProfile::class)
        ->assertSet('isFirstTime', false)
        ->assertSet('country', 'France')
        ->assertSet('city', 'Paris')
        ->assertSet('bio', 'Profil existant');
});

it('adds and removes tech stack items', function () {
    $user = User::factory()->create();

    $component = Livewire::actingAs($user)
        ->test(EditProfile::class)
        ->set('newStackItem', 'Laravel')
        ->call('addStackItem');

    expect($component->get('tech_stack'))->toContain('Laravel');

    $component->call('removeStackItem', 'Laravel');
    expect($component->get('tech_stack'))->not->toContain('Laravel');
});

it('toggles notification preferences', function () {
    $user = User::factory()->create();

    $component = Livewire::actingAs($user)
        ->test(EditProfile::class)
        ->call('toggleNotificationPreference', 'new_answer');

    expect($component->get('notificationPreferences')['new_answer'])->toBeFalse();
});

it('validates the portfolio url format', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(EditProfile::class)
        ->set('portfolio_url', 'not-a-url')
        ->call('save')
        ->assertHasErrors(['portfolio_url' => 'url']);
});
