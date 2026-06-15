<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Partner;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

uses(TestCase::class);

it('caches the active partners and serves them from cache on subsequent calls', function () {
    $seeded = Partner::cachedActive();

    expect($seeded)->toHaveCount(Partner::active()->count());
    expect(Cache::has('partner:active'))->toBeTrue();
});

it('invalidates the cache when a partner is created', function () {
    Partner::cachedActive();

    Partner::create([
        'name'      => 'Nouveau Partenaire',
        'icon'      => 'fa-solid fa-star',
        'type'      => 'community',
        'order'     => 99,
        'is_active' => true,
    ]);

    $refreshed = Partner::cachedActive();

    expect($refreshed->pluck('name'))->toContain('Nouveau Partenaire');
});

it('invalidates the cache when a partner is deleted', function () {
    $partner = Partner::active()->first();

    Partner::cachedActive();

    $partner->delete();

    $refreshed = Partner::cachedActive();

    expect($refreshed->pluck('id'))->not->toContain($partner->id);
});
