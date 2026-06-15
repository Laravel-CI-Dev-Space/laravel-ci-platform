<?php

declare(strict_types=1);

namespace Tests\Feature\Middleware;

use App\Http\Middleware\EnsureMemberDashboardAccess;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpKernel\Exception\HttpException;

function callMemberDashboardMiddleware(User $user, bool $viewingAsMember = false)
{
    $request = Request::create('/dashboard/member');
    $request->setUserResolver(fn () => $user);

    if ($viewingAsMember) {
        Session::put('viewing_as_member', true);
    }

    return (new EnsureMemberDashboardAccess())->handle($request, fn () => response('OK'));
}

it('allows a member to access the member dashboard', function () {
    $user = User::factory()->create();
    $user->assignRole('member');

    $response = callMemberDashboardMiddleware($user);

    expect($response->getContent())->toBe('OK');
});

it('blocks an admin without viewing-as-member session', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    expect(fn () => callMemberDashboardMiddleware($user))->toThrow(HttpException::class);
});

it('allows an admin with viewing-as-member session active', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    $response = callMemberDashboardMiddleware($user, viewingAsMember: true);

    expect($response->getContent())->toBe('OK');
});

it('allows a super-admin with viewing-as-member session active', function () {
    $user = User::factory()->create();
    $user->assignRole('super-admin');

    $response = callMemberDashboardMiddleware($user, viewingAsMember: true);

    expect($response->getContent())->toBe('OK');
});

it('blocks a regular user with no role', function () {
    $user = User::factory()->create();

    expect(fn () => callMemberDashboardMiddleware($user))->toThrow(HttpException::class);
});
