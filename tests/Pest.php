<?php

declare(strict_types=1);

use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind different classes or traits.
|
| Tests\TestCase already applies RefreshDatabase and seeds essential
| reference data (roles/permissions, site settings, vitrine config, ...)
| in its setUp(), so every test extending it starts from a working
| baseline without needing to repeat ->use(RefreshDatabase::class).
|
*/

pest()->extend(TestCase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function something()
{
    // ..
}

function makeMember(array $attributes = []): \App\Models\User
{
    $user = \App\Models\User::factory()->create($attributes);
    $user->assignRole('member');
    \App\Models\Profile::create(['user_id' => $user->id]);

    return $user;
}

function makeCompanyAccount(array $attributes = []): \App\Models\CompanyAccount
{
    return \App\Models\CompanyAccount::create(array_merge([
        'first_name'          => 'Jean',
        'last_name'           => 'Dupont',
        'email'               => fake()->unique()->safeEmail(),
        'password'            => 'secret',
        'position'            => 'RH',
        'status'              => \App\Enums\CompanyAccountStatus::Active,
        'password_changed_at' => now(),
    ], $attributes));
}
