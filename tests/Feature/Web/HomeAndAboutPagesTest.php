<?php

declare(strict_types=1);

namespace Tests\Feature\Web;

use App\Models\Partner;
use App\Models\TeamMember;

it('renders the home page with cached stats and partners', function () {
    $response = $this->get('/');

    $response->assertOk();
    $response->assertSee(Partner::active()->first()->name);
});

it('renders the about page with cached origin, timeline, team and values', function () {
    $response = $this->get('/about');

    $response->assertOk();
    $response->assertSee("D'une conviction à une communauté", false);
    $response->assertSee(TeamMember::active()->first()->fullName());
});
