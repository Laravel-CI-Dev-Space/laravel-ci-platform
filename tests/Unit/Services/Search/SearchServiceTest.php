<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Search;

use App\Models\Article;
use App\Models\JobOffer;
use App\Models\Question;
use App\Services\Search\SearchService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Tests\TestCase;

uses(TestCase::class);

it('returns empty results for a query that is too short', function () {
    $service = new SearchService();

    $result = $service->globalSearch('a');

    expect($result['questions'])->toBeInstanceOf(Collection::class)->toHaveCount(0);
    expect($result['articles'])->toHaveCount(0);
    expect($result['jobs'])->toHaveCount(0);
    expect($result['total'])->toBe(0);
});

it('finds published questions matching the query', function () {
    Question::factory()->create(['title' => 'Comment configurer Laravel Sail', 'body' => 'Une question sur Sail.']);
    Question::factory()->hidden()->create(['title' => 'Question cachée sur Laravel Sail']);

    $service = new SearchService();
    $result  = $service->globalSearch('Sail');

    expect($result['questions'])->toHaveCount(1);
    expect($result['questions']->first()->title)->toBe('Comment configurer Laravel Sail');
    expect($result['total'])->toBe(1);
});

it('finds published articles matching the query', function () {
    Article::factory()->published()->create(['title' => 'Guide complet sur Eloquent ORM']);
    Article::factory()->create(['title' => 'Brouillon sur Eloquent ORM']);

    $service = new SearchService();
    $result  = $service->globalSearch('Eloquent');

    expect($result['articles'])->toHaveCount(1);
    expect($result['articles']->first()->title)->toBe('Guide complet sur Eloquent ORM');
});

it('finds active job offers matching the query', function () {
    JobOffer::factory()->active()->create(['title' => 'Développeur Laravel Senior']);
    JobOffer::factory()->create(['title' => 'Développeur Laravel Junior']);

    $service = new SearchService();
    $result  = $service->globalSearch('Laravel');

    expect($result['jobs'])->toHaveCount(1);
    expect($result['jobs']->first()->title)->toBe('Développeur Laravel Senior');
});

it('returns empty results from search() for a query that is too short', function () {
    $service = new SearchService();

    $result = $service->search('a');

    expect($result['results'])->toHaveCount(0);
    expect($result['total'])->toBe(0);
});

it('paginates results for a specific type', function () {
    Question::factory()->create(['title' => 'Comment déboguer un job en file Laravel']);

    $service = new SearchService();
    $result  = $service->search('Laravel', 'questions');

    expect($result['results'])->toBeInstanceOf(LengthAwarePaginator::class);
    expect($result['total'])->toBe(1);
});

it('merges results from all types when searching everything', function () {
    Question::factory()->create(['title' => 'Question Laravel Octane']);
    Article::factory()->published()->create(['title' => 'Article Laravel Octane']);
    JobOffer::factory()->active()->create(['title' => 'Offre Laravel Octane']);

    $service = new SearchService();
    $result  = $service->search('Octane', 'all');

    expect($result['results'])->toHaveCount(3);
    expect($result['total'])->toBe(3);

    $types = $result['results']->pluck('type')->all();
    expect($types)->toContain('question', 'article', 'job');
});

it('suggests at most 3 results per type', function () {
    Question::factory()->count(5)->create(['title' => 'Question sur Symfony Components']);

    $service = new SearchService();
    $result  = $service->suggest('Symfony');

    expect($result['questions'])->toHaveCount(3);
});
