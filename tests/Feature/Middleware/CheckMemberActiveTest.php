<?php

declare(strict_types=1);

namespace Tests\Feature\Middleware;

use App\Http\Middleware\CheckMemberActive;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

it('lets a guest through', function () {
    $response = (new CheckMemberActive())->handle(Request::create('/'), fn () => response('OK'));

    expect($response->getContent())->toBe('OK');
});

it('logs out and redirects a banned user', function () {
    $user = User::factory()->create(['is_active' => false]);
    $this->actingAs($user);

    $response = (new CheckMemberActive())->handle(Request::create('/'), fn () => response('OK'));

    expect($response->isRedirect(route('login')))->toBeTrue();
    expect(auth()->check())->toBeFalse();
});

it('auto-lifts an expired suspension and lets the request through', function () {
    $user = User::factory()->create([
        'is_active'        => true,
        'suspended_until'  => Carbon::now()->subDay(),
    ]);
    $this->actingAs($user);

    $response = (new CheckMemberActive())->handle(Request::create('/'), fn () => response('OK'));

    expect($response->getContent())->toBe('OK');
    expect($user->refresh()->suspended_until)->toBeNull();
});

it('logs out and redirects a currently suspended user', function () {
    $user = User::factory()->create([
        'is_active'       => true,
        'suspended_until' => Carbon::now()->addDays(3),
    ]);
    $this->actingAs($user);

    $request = Request::create('/');
    $request->setLaravelSession($this->app['session']->driver());

    $response = (new CheckMemberActive())->handle($request, fn () => response('OK'));

    expect($response->isRedirect(route('login')))->toBeTrue();
    expect(auth()->check())->toBeFalse();
});

it('lets an active, non-suspended user through', function () {
    $user = User::factory()->create(['is_active' => true, 'suspended_until' => null]);
    $this->actingAs($user);

    $response = (new CheckMemberActive())->handle(Request::create('/'), fn () => response('OK'));

    expect($response->getContent())->toBe('OK');
});
