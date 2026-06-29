<?php

it('renders the join page with benefits and faq', function () {
    $this->get(route('join'))
        ->assertOk()
        ->assertSee('Rejoins la communauté Laravel ivoirienne')
        ->assertSee('Continuer avec GitHub')
        ->assertSee("L'adhésion est-elle gratuite", false);
});

it('exposes seo meta tags on the home page', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('<meta name="description"', false)
        ->assertSee('property="og:title"', false)
        ->assertSee('property="og:image"', false)
        ->assertSee('rel="canonical"', false);
});

it('marks the login page as noindex', function () {
    $this->get(route('login'))
        ->assertOk()
        ->assertSee('name="robots" content="noindex, nofollow"', false);
});

it('serves a robots.txt pointing to the sitemap', function () {
    $this->get('/robots.txt')
        ->assertOk()
        ->assertSee('Sitemap:')
        ->assertSee('sitemap.xml');
});

it('links to the join page from the header', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee(route('join'), false);
});

it('generates a sitemap file containing the main vitrine routes', function () {
    $this->artisan('sitemap:generate')->assertSuccessful();

    $path = public_path('sitemap.xml');

    expect($path)->toBeFile();

    $content = file_get_contents($path);

    expect($content)
        ->toContain(route('home'))
        ->toContain(route('join'))
        ->toContain(route('about'));

    @unlink($path);
});
