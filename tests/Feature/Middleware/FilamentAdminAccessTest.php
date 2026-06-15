<?php

declare(strict_types=1);

namespace Tests\Feature\Middleware;

use App\Enums\UserPermission;
use App\Http\Middleware\FilamentAdminAccess;
use App\Models\User;
use Illuminate\Http\Request;

it('lets a super-admin through', function () {
    $user = User::factory()->create();
    $user->assignRole('super-admin');
    $this->actingAs($user);

    $response = (new FilamentAdminAccess())->handle(Request::create('/admin'), fn () => response('OK'));

    expect($response->getContent())->toBe('OK');
});

it('lets a user with the admin.access permission through', function () {
    $user = User::factory()->create();
    $user->givePermissionTo(UserPermission::AdminAccess->value);
    $this->actingAs($user);

    $response = (new FilamentAdminAccess())->handle(Request::create('/admin'), fn () => response('OK'));

    expect($response->getContent())->toBe('OK');
});

it('redirects a regular user without admin role or permission', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = (new FilamentAdminAccess())->handle(Request::create('/admin'), fn () => response('OK'));

    expect($response->isRedirect(route('dashboard')))->toBeTrue();
});

it('lets an unauthenticated request through', function () {
    $response = (new FilamentAdminAccess())->handle(Request::create('/admin'), fn () => response('OK'));

    expect($response->getContent())->toBe('OK');
});
