<?php

declare(strict_types=1);


it('redirects a company account with a temporary password to the change password page', function () {
    $account = makeCompanyAccount(['password_changed_at' => null]);

    $response = $this->actingAs($account, 'company')->get(route('company.offers.index'));

    $response->assertRedirect(route('company.password.change'));
    $response->assertSessionHas('warning');
});

it('allows access to the change password page itself while a password change is pending', function () {
    $account = makeCompanyAccount(['password_changed_at' => null]);

    $response = $this->actingAs($account, 'company')->get(route('company.password.change'));

    $response->assertOk();
});

it('does not redirect a company account that already changed its password', function () {
    $account = makeCompanyAccount(['password_changed_at' => now()]);

    $response = $this->actingAs($account, 'company')->get(route('company.dashboard'));

    $response->assertRedirect('/company/portal');
});
