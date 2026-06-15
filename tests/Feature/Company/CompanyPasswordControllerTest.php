<?php

declare(strict_types=1);


it('displays the change password form', function () {
    $account = makeCompanyAccount(['password_changed_at' => null]);

    $response = $this->actingAs($account, 'company')->get(route('company.password.change'));

    $response->assertOk();
    $response->assertViewIs('company.auth.change-password');
});

it('updates the password and marks password_changed_at', function () {
    $account = makeCompanyAccount(['password_changed_at' => null]);

    $response = $this->actingAs($account, 'company')->post(route('company.password.update'), [
        'password'              => 'a-new-strong-password',
        'password_confirmation' => 'a-new-strong-password',
    ]);

    $response->assertRedirect('/company/portal');
    $response->assertSessionHas('success');

    $account->refresh();
    expect($account->password_changed_at)->not->toBeNull();
    expect($account->mustChangePassword())->toBeFalse();
});

it('requires the password confirmation to match and a minimum length', function () {
    $account = makeCompanyAccount(['password_changed_at' => null]);

    $response = $this->actingAs($account, 'company')->post(route('company.password.update'), [
        'password'              => 'short',
        'password_confirmation' => 'different',
    ]);

    $response->assertSessionHasErrors('password');
});
