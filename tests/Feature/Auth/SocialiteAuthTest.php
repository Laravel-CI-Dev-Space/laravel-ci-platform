<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Mail\WelcomeMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Mockery;
use Laravel\Socialite\Contracts\User as SocialiteUserContract;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\InvalidStateException;

function mockGithubUser(string $id, string $email, string $name, string $username): void
{
    $githubUser = Mockery::mock(SocialiteUserContract::class);
    $githubUser->shouldReceive('getId')->andReturn($id);
    $githubUser->shouldReceive('getEmail')->andReturn($email);
    $githubUser->shouldReceive('getName')->andReturn($name);
    $githubUser->shouldReceive('getNickname')->andReturn($username);
    $githubUser->shouldReceive('getAvatar')->andReturn('https://avatars.githubusercontent.com/u/'.$id);

    $provider = Mockery::mock(AbstractProvider::class);
    $provider->shouldReceive('user')->andReturn($githubUser);

    Socialite::shouldReceive('driver')->with('github')->andReturn($provider);
}

it('creates a new member account on first GitHub login', function () {
    Mail::fake();
    mockGithubUser('111', 'newdev@example.com', 'New Dev', 'newdev');

    $response = $this->get(route('auth.github.callback'));

    $response->assertRedirect(route('dashboard'));
    $this->assertAuthenticated();

    $user = User::where('github_id', '111')->first();
    expect($user)->not->toBeNull();
    expect($user->email)->toBe('newdev@example.com');
    expect($user->hasRole('member'))->toBeTrue();

    Mail::assertQueued(WelcomeMail::class);
});

it('logs in an existing user and refreshes GitHub data', function () {
    $user = User::factory()->create([
        'github_id'       => '222',
        'github_username' => 'olddev',
        'name'            => 'Old Name',
    ]);

    mockGithubUser('222', $user->email, 'New Name', 'newdev-handle');

    $response = $this->get(route('auth.github.callback'));

    $response->assertRedirect(route('dashboard'));
    $this->assertAuthenticatedAs($user);

    expect($user->refresh()->name)->toBe('New Name');
    expect($user->github_username)->toBe('newdev-handle');
});

it('rejects login for a banned user', function () {
    $user = User::factory()->create([
        'github_id' => '333',
        'is_active' => false,
    ]);

    mockGithubUser('333', $user->email, $user->name, 'banneduser');

    $response = $this->get(route('auth.github.callback'));

    $response->assertRedirect(route('login'));
    $response->assertSessionHas('error');
    $this->assertGuest();
});

it('rejects registration when the email is already used by another account', function () {
    User::factory()->create([
        'email'     => 'taken@example.com',
        'github_id' => '999',
    ]);

    mockGithubUser('444', 'taken@example.com', 'Conflict User', 'conflictuser');

    $response = $this->get(route('auth.github.callback'));

    $response->assertRedirect(route('login'));
    $response->assertSessionHas('error');
    $this->assertGuest();
});

it('handles an invalid OAuth state gracefully', function () {
    $provider = Mockery::mock(AbstractProvider::class);
    $provider->shouldReceive('user')->andThrow(new InvalidStateException('invalid state'));

    Socialite::shouldReceive('driver')->with('github')->andReturn($provider);

    $response = $this->get(route('auth.github.callback'));

    $response->assertRedirect(route('login'));
    $response->assertSessionHas('error');
    $this->assertGuest();
});
