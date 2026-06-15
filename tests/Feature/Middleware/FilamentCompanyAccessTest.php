<?php

declare(strict_types=1);

namespace Tests\Feature\Middleware;

use App\Enums\CompanyAccountStatus;
use App\Http\Middleware\FilamentCompanyAccess;
use Illuminate\Http\Request;

it('redirects to company.login when not authenticated', function () {
    $response = (new FilamentCompanyAccess())->handle(Request::create('/company/portal'), fn () => response('OK'));

    expect($response->isRedirect(route('company.login')))->toBeTrue();
});

it('logs out and redirects when the account is not active', function () {
    $account = makeCompanyAccount(['status' => CompanyAccountStatus::Suspended]);
    $this->actingAs($account, 'company');

    $request = Request::create('/company/portal');
    $request->setLaravelSession($this->app['session']->driver());

    $response = (new FilamentCompanyAccess())->handle($request, fn () => response('OK'));

    expect($response->isRedirect(route('company.login')))->toBeTrue();
    expect(auth('company')->check())->toBeFalse();
});

it('redirects to password change when the password must be changed', function () {
    $account = makeCompanyAccount(['password_changed_at' => null]);
    $this->actingAs($account, 'company');

    $response = (new FilamentCompanyAccess())->handle(Request::create('/company/portal'), fn () => response('OK'));

    expect($response->isRedirect(route('company.password.change')))->toBeTrue();
});

it('lets an active account with a changed password through', function () {
    $account = makeCompanyAccount();
    $this->actingAs($account, 'company');

    $response = (new FilamentCompanyAccess())->handle(Request::create('/company/portal'), fn () => response('OK'));

    expect($response->getContent())->toBe('OK');
});
