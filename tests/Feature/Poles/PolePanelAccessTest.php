<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\Pole;
use App\Models\PoleMember;
use App\Models\User;
use Filament\Panel;

function mockPanel(string $id): Panel
{
    $panel = Mockery::mock(Panel::class);
    $panel->shouldReceive('getId')->andReturn($id);

    return $panel;
}

// ─── /admin panel ────────────────────────────────────────────────────────────

it('super-admin can access the admin panel', function () {
    $user = User::factory()->create();
    $user->assignRole(UserRole::SuperAdmin->value);

    expect($user->canAccessPanel(mockPanel('admin')))->toBeTrue();
});

it('a regular member cannot access the admin panel', function () {
    $user = User::factory()->create();
    $user->assignRole(UserRole::Member->value);

    expect($user->canAccessPanel(mockPanel('admin')))->toBeFalse();
});

it('an active pole member cannot access the admin panel without super-admin role', function () {
    $pole = Pole::factory()->withSlug('evenements')->create();
    $user = User::factory()->create();
    PoleMember::factory()->forPole($pole)->forUser($user)->actif()->create();

    expect($user->canAccessPanel(mockPanel('admin')))->toBeFalse();
});

// ─── /espace-pole panel ──────────────────────────────────────────────────────

it('an active pole member can access the espace-pole panel', function () {
    $pole = Pole::factory()->withSlug('communication')->create();
    $user = User::factory()->create();
    PoleMember::factory()->forPole($pole)->forUser($user)->actif()->create();

    expect($user->canAccessPanel(mockPanel('espace-pole')))->toBeTrue();
});

it('an inactive pole member cannot access the espace-pole panel', function () {
    $pole = Pole::factory()->withSlug('communication')->create();
    $user = User::factory()->create();
    PoleMember::factory()->forPole($pole)->forUser($user)->inactif()->create();

    expect($user->canAccessPanel(mockPanel('espace-pole')))->toBeFalse();
});

it('a super-admin without a pole membership cannot access the espace-pole panel', function () {
    $user = User::factory()->create();
    $user->assignRole(UserRole::SuperAdmin->value);

    expect($user->canAccessPanel(mockPanel('espace-pole')))->toBeFalse();
});

it('a user without any membership cannot access the espace-pole panel', function () {
    $user = User::factory()->create();

    expect($user->canAccessPanel(mockPanel('espace-pole')))->toBeFalse();
});

it('returns false for unknown panel ids', function () {
    $user = User::factory()->create();
    $user->assignRole(UserRole::SuperAdmin->value);

    expect($user->canAccessPanel(mockPanel('unknown-panel')))->toBeFalse();
});

// ─── activePoleMember() ──────────────────────────────────────────────────────

it('activePoleMember returns the member when status is actif', function () {
    $pole = Pole::factory()->withSlug('partenariat')->create();
    $user = User::factory()->create();
    PoleMember::factory()->forPole($pole)->forUser($user)->actif()->create();

    expect($user->fresh()->activePoleMember())->not->toBeNull();
});

it('activePoleMember returns null when status is inactif', function () {
    $pole = Pole::factory()->withSlug('partenariat')->create();
    $user = User::factory()->create();
    PoleMember::factory()->forPole($pole)->forUser($user)->inactif()->create();

    expect($user->fresh()->activePoleMember())->toBeNull();
});

it('activePoleMember returns null when user has no pole membership', function () {
    $user = User::factory()->create();

    expect($user->activePoleMember())->toBeNull();
});

// ─── Header button visibility ─────────────────────────────────────────────────

it('shows the espace pole button in header for an active pole member', function () {
    $pole = Pole::factory()->withSlug('communication')->create();
    $user = User::factory()->create();
    PoleMember::factory()->forPole($pole)->forUser($user)->actif()->create();

    $response = $this->actingAs($user)->get(route('home'));

    $response->assertSee('Espace pôle');
    $response->assertSee('/espace-pole', escape: false);
});

it('hides the espace pole button in header for a user without pole membership', function () {
    $user = User::factory()->create();
    $user->assignRole(UserRole::Member->value);

    $response = $this->actingAs($user)->get(route('home'));

    $response->assertDontSee('Espace pôle');
});

it('hides the espace pole button in header for an inactive pole member', function () {
    $pole = Pole::factory()->withSlug('communication')->create();
    $user = User::factory()->create();
    PoleMember::factory()->forPole($pole)->forUser($user)->inactif()->create();

    $response = $this->actingAs($user)->get(route('home'));

    $response->assertDontSee('Espace pôle');
});
