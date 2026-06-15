<?php

declare(strict_types=1);

namespace Tests\Feature\Middleware;

use App\Enums\CompanyAccountStatus;
use App\Http\Middleware\CompanyActive;
use Illuminate\Http\Request;

it('redirects to company.login when no account is authenticated', function () {
    $response = (new CompanyActive())->handle(Request::create('/company/dashboard'), fn () => response('OK'));

    expect($response->isRedirect(route('company.login')))->toBeTrue();
});

it('logs out and redirects a pending account', function () {
    $account = makeCompanyAccount(['status' => CompanyAccountStatus::Pending]);
    $this->actingAs($account, 'company');

    $response = (new CompanyActive())->handle(Request::create('/company/dashboard'), fn () => response('OK'));

    expect($response->isRedirect(route('company.login')))->toBeTrue();
    expect(auth('company')->check())->toBeFalse();
});

it('logs out and redirects a suspended account', function () {
    $account = makeCompanyAccount(['status' => CompanyAccountStatus::Suspended]);
    $this->actingAs($account, 'company');

    $response = (new CompanyActive())->handle(Request::create('/company/dashboard'), fn () => response('OK'));

    expect($response->isRedirect(route('company.login')))->toBeTrue();
    expect(auth('company')->check())->toBeFalse();
});

it('logs out and redirects a rejected account', function () {
    $account = makeCompanyAccount(['status' => CompanyAccountStatus::Rejected]);
    $this->actingAs($account, 'company');

    $response = (new CompanyActive())->handle(Request::create('/company/dashboard'), fn () => response('OK'));

    expect($response->isRedirect(route('company.login')))->toBeTrue();
    expect(auth('company')->check())->toBeFalse();
});

it('lets an active account through', function () {
    $account = makeCompanyAccount(['status' => CompanyAccountStatus::Active]);
    $this->actingAs($account, 'company');

    $response = (new CompanyActive())->handle(Request::create('/company/dashboard'), fn () => response('OK'));

    expect($response->getContent())->toBe('OK');
});
