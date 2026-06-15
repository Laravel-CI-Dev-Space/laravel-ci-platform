<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Blog;

use App\Enums\ArticleStatus;
use App\Models\Article;
use App\Models\Tag;
use App\Models\User;
use App\Services\Blog\ArticleService;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

uses(TestCase::class);

it('creates an article as a draft and attaches tags', function () {
    $user = User::factory()->create();
    $tag  = Tag::create(['name' => 'Testing', 'slug' => 'testing-article', 'scope' => 'blog']);

    $article = app(ArticleService::class)->createArticle($user, [
        'title' => 'Comment tester une application Laravel',
        'body'  => "## Introduction\n\nVoici comment écrire des tests Pest efficaces.",
        'level' => 'beginner',
        'tags'  => [$tag->id],
    ]);

    expect($article->status)->toBe(ArticleStatus::Draft);
    expect($article->user_id)->toBe($user->id);
    expect($article->tags->pluck('id'))->toContain($tag->id);
    expect($article->body_html)->toContain('<h2>Introduction</h2>');
    expect($tag->refresh()->usage_count)->toBe(1);
});

it('lets the author submit a draft article for review', function () {
    $user    = User::factory()->create();
    $article = Article::factory()->for($user, 'author')->create(['status' => ArticleStatus::Draft]);

    app(ArticleService::class)->submitForReview($user, $article);

    expect($article->refresh()->status)->toBe(ArticleStatus::Pending);
});

it('lets the author resubmit a rejected article', function () {
    $user    = User::factory()->create();
    $article = Article::factory()->for($user, 'author')->create(['status' => ArticleStatus::Rejected]);

    app(ArticleService::class)->submitForReview($user, $article);

    expect($article->refresh()->status)->toBe(ArticleStatus::Pending);
});

it('forbids submitting another user\'s article', function () {
    $owner  = User::factory()->create();
    $other  = User::factory()->create();
    $article = Article::factory()->for($owner, 'author')->create(['status' => ArticleStatus::Draft]);

    expect(fn () => app(ArticleService::class)->submitForReview($other, $article))
        ->toThrow(HttpException::class);
});

it('rejects submitting an article that is already pending', function () {
    $user    = User::factory()->create();
    $article = Article::factory()->for($user, 'author')->create(['status' => ArticleStatus::Pending]);

    expect(fn () => app(ArticleService::class)->submitForReview($user, $article))
        ->toThrow(HttpException::class);
});

it('publishes a pending article', function () {
    $author   = User::factory()->create();
    $reviewer = User::factory()->create();
    $article  = Article::factory()->for($author, 'author')->create(['status' => ArticleStatus::Pending]);

    app(ArticleService::class)->publish($reviewer, $article);

    $article->refresh();
    expect($article->status)->toBe(ArticleStatus::Published);
    expect($article->published_at)->not->toBeNull();
    expect($article->reviewed_by)->toBe($reviewer->id);
});

it('rejects a pending article with a reason', function () {
    $author   = User::factory()->create();
    $reviewer = User::factory()->create();
    $article  = Article::factory()->for($author, 'author')->create(['status' => ArticleStatus::Pending]);

    app(ArticleService::class)->reject($reviewer, $article, 'Contenu hors sujet.');

    $article->refresh();
    expect($article->status)->toBe(ArticleStatus::Rejected);
    expect($article->rejection_reason)->toBe('Contenu hors sujet.');
    expect($article->reviewed_by)->toBe($reviewer->id);
});

it('lists pending articles for moderation', function () {
    Article::factory()->create(['status' => ArticleStatus::Pending]);
    Article::factory()->create(['status' => ArticleStatus::Draft]);
    Article::factory()->create(['status' => ArticleStatus::Published, 'published_at' => now()]);

    $pending = app(ArticleService::class)->getPendingArticles();

    expect($pending)->toHaveCount(1);
    expect($pending->first()->status)->toBe(ArticleStatus::Pending);
});
