<?php

declare(strict_types=1);

use App\Models\JobOffer;

test('guest can view an active job offer detail page', function () {
    $offer = JobOffer::factory()->active()->create([
        'title' => 'Détail offre publique',
    ]);

    $response = $this->get(route('jobs.show', $offer));

    $response->assertOk();
    $response->assertSee('Détail offre publique');
    $response->assertSee('/jobs/' . $offer->slug, false);
});

test('job offer detail page uses slug in url not id', function () {
    $offer = JobOffer::factory()->active()->create([
        'title' => 'Développeur Laravel',
    ]);

    $this->get('/jobs/' . $offer->id)->assertNotFound();
    $this->get('/jobs/' . $offer->slug)->assertOk();
});

test('guest cannot view a draft job offer', function () {
    $offer = JobOffer::factory()->draft()->create();

    $this->get(route('jobs.show', $offer))->assertForbidden();
});

test('guest cannot view an expired job offer', function () {
    $offer = JobOffer::factory()->expired()->create();

    $this->get(route('jobs.show', $offer))->assertForbidden();
});

test('guest cannot apply to an expired job offer', function () {
    $offer = JobOffer::factory()->expired()->create();

    $this->post(route('jobs.apply', $offer))->assertRedirect(route('login'));
});
