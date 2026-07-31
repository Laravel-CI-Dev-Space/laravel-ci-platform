<?php

declare(strict_types=1);

namespace Tests\Unit\Enums;

use App\Enums\UserRole;
use Tests\TestCase;

uses(TestCase::class);

it('does not contain Admin or Moderator roles', function () {
    $values = UserRole::values();

    expect($values)->not->toContain('admin');
    expect($values)->not->toContain('moderator');
});

it('contains the 5 pole roles', function () {
    $poleValues = UserRole::poleRoleValues();

    expect($poleValues)->toContain('pole-communication');
    expect($poleValues)->toContain('pole-evenements');
    expect($poleValues)->toContain('pole-tech-formation');
    expect($poleValues)->toContain('pole-employabilite');
    expect($poleValues)->toContain('pole-partenariat');
    expect($poleValues)->toHaveCount(5);
});

it('contains super-admin, member and company roles', function () {
    $values = UserRole::values();

    expect($values)->toContain('super-admin');
    expect($values)->toContain('member');
    expect($values)->toContain('company');
});

it('has 8 roles in total', function () {
    expect(UserRole::values())->toHaveCount(8);
});

it('can resolve a pole role from its value', function () {
    expect(UserRole::tryFrom('pole-communication'))->toBe(UserRole::PoleCommunication);
    expect(UserRole::tryFrom('pole-tech-formation'))->toBe(UserRole::PoleTechFormation);
    expect(UserRole::tryFrom('admin'))->toBeNull();
});
