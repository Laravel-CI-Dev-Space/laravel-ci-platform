<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\AboutOriginSection;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

uses(TestCase::class);

it('caches the active origin section', function () {
    $section = AboutOriginSection::cachedActive();

    expect($section)->not->toBeNull();
    expect($section->title)->toBe(AboutOriginSection::where('is_active', true)->first()->title);
    expect(Cache::has('about_origin_section:active'))->toBeTrue();
});

it('invalidates the cache when the origin section is updated', function () {
    AboutOriginSection::cachedActive();

    $section = AboutOriginSection::where('is_active', true)->first();
    $section->update(['title' => 'Nouveau titre']);

    $refreshed = AboutOriginSection::cachedActive();

    expect($refreshed->title)->toBe('Nouveau titre');
});
