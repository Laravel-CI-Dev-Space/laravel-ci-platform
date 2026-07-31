<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\Pole;
use App\Models\PoleMember;
use App\Models\User;

it('assigns the correct role when an active pole member is linked to a user', function () {
    $pole = Pole::factory()->withSlug('communication')->create();
    $user = User::factory()->create();

    PoleMember::factory()->forPole($pole)->forUser($user)->actif()->create();

    expect($user->fresh()->hasRole('pole-communication'))->toBeTrue();
});

it('assigns the role matching the pole slug', function () {
    $pole = Pole::factory()->withSlug('evenements')->create();
    $user = User::factory()->create();

    PoleMember::factory()->forPole($pole)->forUser($user)->actif()->create();

    expect($user->fresh()->hasRole('pole-evenements'))->toBeTrue();
    expect($user->fresh()->hasRole('pole-communication'))->toBeFalse();
});

it('does not assign a role when the member status is inactif', function () {
    $pole = Pole::factory()->withSlug('communication')->create();
    $user = User::factory()->create();

    PoleMember::factory()->forPole($pole)->forUser($user)->inactif()->create();

    expect($user->fresh()->hasRole('pole-communication'))->toBeFalse();
});

it('revokes the pole role when the pole member is deleted', function () {
    $pole   = Pole::factory()->withSlug('communication')->create();
    $user   = User::factory()->create();
    $member = PoleMember::factory()->forPole($pole)->forUser($user)->actif()->create();

    expect($user->fresh()->hasRole('pole-communication'))->toBeTrue();

    $member->delete();

    expect($user->fresh()->hasRole('pole-communication'))->toBeFalse();
});

it('revokes the pole role when the member status changes to inactif', function () {
    $pole   = Pole::factory()->withSlug('communication')->create();
    $user   = User::factory()->create();
    $member = PoleMember::factory()->forPole($pole)->forUser($user)->actif()->create();

    expect($user->fresh()->hasRole('pole-communication'))->toBeTrue();

    $member->update(['status' => 'inactif']);

    expect($user->fresh()->hasRole('pole-communication'))->toBeFalse();
});

it('does not throw when pole member has no linked user', function () {
    $pole = Pole::factory()->withSlug('communication')->create();

    expect(fn () => PoleMember::factory()->forPole($pole)->actif()->create())
        ->not->toThrow(Throwable::class);
});

it('does not assign a role when the pole has no slug', function () {
    $pole = Pole::factory()->create(['slug' => null]);
    $user = User::factory()->create();

    PoleMember::factory()->forPole($pole)->forUser($user)->actif()->create();

    $poleValues = UserRole::poleRoleValues();
    foreach ($poleValues as $roleValue) {
        expect($user->fresh()->hasRole($roleValue))->toBeFalse(
            "User should not have role {$roleValue} when pole has no slug"
        );
    }
});
