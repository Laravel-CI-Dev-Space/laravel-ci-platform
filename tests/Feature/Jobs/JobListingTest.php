<?php

declare(strict_types=1);

use App\Enums\Jobs\JobOfferStatus;
use App\Enums\Jobs\JobOfferType;
use App\Models\JobOffer;
use App\Models\JobSkill;

test('guest can browse active job offers', function () {
    $visible = JobOffer::factory()->active()->create(['title' => 'Développeur Laravel visible']);

    JobOffer::factory()->draft()->create(['title' => 'Brouillon secret']);
    JobOffer::factory()->expired()->create(['title' => 'Offre expirée']);

    $response = $this->get(route('jobs.index'));

    $response->assertOk();
    $response->assertSee($visible->title);
    $response->assertDontSee('Brouillon secret');
    $response->assertDontSee('Offre expirée');
});

test('guest can filter job offers by type', function () {
    JobOffer::factory()->active()->create([
        'title' => 'Poste CDI Abidjan',
        'type'  => JobOfferType::CDI,
    ]);

    JobOffer::factory()->active()->remote()->create([
        'title' => 'Poste remote',
    ]);

    $response = $this->get(route('jobs.index', ['type' => 'cdi']));

    $response->assertOk();
    $response->assertSee('Poste CDI Abidjan');
    $response->assertDontSee('Poste remote');
});

test('guest can filter job offers by remote flag', function () {
    JobOffer::factory()->active()->remote()->create(['title' => '100% Remote']);

    JobOffer::factory()->active()->create([
        'title'    => 'Sur site Abidjan',
        'type'     => JobOfferType::CDI,
        'location' => 'Abidjan',
    ]);

    $response = $this->get(route('jobs.index', ['remote' => 1]));

    $response->assertOk();
    $response->assertSee('100% Remote');
    $response->assertDontSee('Sur site Abidjan');
});

test('guest can filter job offers by skill', function () {
    $laravel = JobSkill::factory()->create(['name' => 'Laravel', 'slug' => 'laravel']);
    $vue     = JobSkill::factory()->create(['name' => 'Vue.js', 'slug' => 'vue-js']);

    $withLaravel = JobOffer::factory()->active()->create(['title' => 'Offre Laravel']);
    $withLaravel->skills()->attach($laravel);

    $withVue = JobOffer::factory()->active()->create(['title' => 'Offre Vue']);
    $withVue->skills()->attach($vue);

    $response = $this->get(route('jobs.index', ['skill' => $laravel->slug]));

    $response->assertOk();
    $response->assertSee('Offre Laravel');
    $response->assertDontSee('Offre Vue');
});
