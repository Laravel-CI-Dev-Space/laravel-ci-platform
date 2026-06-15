<?php

declare(strict_types=1);

namespace Tests\Feature\Middleware;

use App\Http\Middleware\CompanyAuthenticated;
use Illuminate\Http\Request;

it('redirects to company.login when not authenticated', function () {
    $response = (new CompanyAuthenticated())->handle(Request::create('/company/dashboard'), fn () => response('OK'));

    expect($response->isRedirect(route('company.login')))->toBeTrue();
});

it('lets an authenticated company account through', function () {
    $account = makeCompanyAccount();
    $this->actingAs($account, 'company');

    $response = (new CompanyAuthenticated())->handle(Request::create('/company/dashboard'), fn () => response('OK'));

    expect($response->getContent())->toBe('OK');
});
