<?php

declare(strict_types=1);


it('displays the company login form', function () {
    $response = $this->get(route('company.login'));

    $response->assertOk();
    $response->assertViewIs('company.auth.login');
});

it('logs in a company account with valid credentials and redirects to the portal', function () {
    $account = makeCompanyAccount(['email' => 'acme@example.com', 'password' => 'super-secret']);

    $response = $this->post(route('company.login.submit'), [
        'email'    => 'acme@example.com',
        'password' => 'super-secret',
    ]);

    $response->assertRedirect('/company/portal');
    $this->assertAuthenticatedAs($account, 'company');
});

it('rejects invalid credentials', function () {
    makeCompanyAccount(['email' => 'acme@example.com', 'password' => 'super-secret']);

    $response = $this->post(route('company.login.submit'), [
        'email'    => 'acme@example.com',
        'password' => 'wrong-password',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('error');
    $this->assertGuest('company');
});

it('logs out a company account', function () {
    $account = makeCompanyAccount();

    $response = $this->actingAs($account, 'company')->post(route('company.logout'));

    $response->assertRedirect(route('company.login'));
    $response->assertSessionHas('success');
    $this->assertGuest('company');
});
