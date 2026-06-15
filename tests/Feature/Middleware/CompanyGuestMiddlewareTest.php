<?php

declare(strict_types=1);


it('redirects an authenticated company account away from the login page', function () {
    $account = makeCompanyAccount();

    $response = $this->actingAs($account, 'company')->get(route('company.login'));

    $response->assertRedirect(route('company.dashboard'));
});

it('allows a guest to access the login page', function () {
    $response = $this->get(route('company.login'));

    $response->assertOk();
});

it('redirects an authenticated company account away from the register page', function () {
    $account = makeCompanyAccount();

    $response = $this->actingAs($account, 'company')->get(route('company.register'));

    $response->assertRedirect(route('company.dashboard'));
});
