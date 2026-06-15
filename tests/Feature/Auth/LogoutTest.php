<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Models\User;

it('logs the user out and redirects to home', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->post(route('logout'));

    $response->assertRedirect(route('home'));
    $this->assertGuest();
});
