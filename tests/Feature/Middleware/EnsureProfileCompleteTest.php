<?php

declare(strict_types=1);

namespace Tests\Feature\Middleware;

use App\Http\Middleware\EnsureProfileComplete;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;

function makeProfileCompleteRequest(User $user, string $routeName = 'dashboard', string $uri = '/dashboard'): Request
{
    $route = new Route('GET', $uri, []);
    $route->name($routeName);

    $request = Request::create($uri);
    $request->setRouteResolver(fn () => $route);
    $request->setUserResolver(fn () => $user);

    return $request;
}

it('redirects to profile.edit when the user has no profile', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $request  = makeProfileCompleteRequest($user);
    $response = (new EnsureProfileComplete())->handle($request, fn () => response('OK'));

    expect($response->isRedirect(route('profile.edit')))->toBeTrue();
});

it('lets the request through when the user has a profile', function () {
    $user = User::factory()->create();
    Profile::create(['user_id' => $user->id]);
    $this->actingAs($user);

    $request  = makeProfileCompleteRequest($user);
    $response = (new EnsureProfileComplete())->handle($request, fn () => response('OK'));

    expect($response->getContent())->toBe('OK');
});

it('does not redirect when already on the profile.edit route', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $request  = makeProfileCompleteRequest($user, 'profile.edit', '/profil/completer');
    $response = (new EnsureProfileComplete())->handle($request, fn () => response('OK'));

    expect($response->getContent())->toBe('OK');
});

it('does not redirect on admin* routes', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $request  = makeProfileCompleteRequest($user, 'filament.admin.pages.dashboard', '/admin');
    $response = (new EnsureProfileComplete())->handle($request, fn () => response('OK'));

    expect($response->getContent())->toBe('OK');
});
