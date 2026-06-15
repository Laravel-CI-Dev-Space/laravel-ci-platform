<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\HomeStat;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

uses(TestCase::class);

it('resolves and caches the auto-counted value for a stat', function () {
    $stat = HomeStat::where('label', 'Members')->first();

    expect($stat->resolvedValue())->toBe(User::count());
});

it('serves the auto-counted value from cache without re-querying', function () {
    $stat = HomeStat::where('label', 'Members')->first();

    $stat->resolvedValue();

    User::factory()->create();

    DB::enableQueryLog();
    $cached = $stat->resolvedValue();
    $queries = DB::getQueryLog();
    DB::disableQueryLog();

    expect($cached)->toBe(User::count() - 1);
    expect($queries)->toBeEmpty();
});
