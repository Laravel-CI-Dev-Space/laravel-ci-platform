<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\Blog;

use App\Http\Requests\Blog\UpdateArticleRequest;
use App\Models\Article;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

uses(TestCase::class);

function requestWithArticleRoute(UpdateArticleRequest $request, Article $article): UpdateArticleRequest
{
    $request->setRouteResolver(fn () => new class($article)
    {
        public function __construct(private Article $article) {}

        public function parameter(string $key, $default = null)
        {
            return $key === 'article' ? $this->article : $default;
        }
    });

    return $request;
}

it('authorizes the article author to update it', function () {
    $user    = User::factory()->create();
    $article = Article::factory()->for($user, 'author')->create();

    $request = new UpdateArticleRequest();
    $request->setUserResolver(fn () => $user);
    requestWithArticleRoute($request, $article);

    expect($request->authorize())->toBeTrue();
});

it('authorizes an admin to update someone else\'s article', function () {
    $author = User::factory()->create();
    $admin  = User::factory()->create();
    $admin->assignRole('admin');
    $article = Article::factory()->for($author, 'author')->create();

    $request = new UpdateArticleRequest();
    $request->setUserResolver(fn () => $admin);
    requestWithArticleRoute($request, $article);

    expect($request->authorize())->toBeTrue();
});

it('denies an unrelated member from updating the article', function () {
    $author = User::factory()->create();
    $other  = User::factory()->create();
    $article = Article::factory()->for($author, 'author')->create();

    $request = new UpdateArticleRequest();
    $request->setUserResolver(fn () => $other);
    requestWithArticleRoute($request, $article);

    expect($request->authorize())->toBeFalse();
});

it('validates the required fields and minimum lengths', function () {
    $request = new UpdateArticleRequest();

    $validator = Validator::make([
        'title' => 'Court',
        'body'  => 'Trop court',
        'level' => 'beginner',
        'tags'  => [],
    ], $request->rules(), $request->messages());

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('title'))->toBeTrue();
    expect($validator->errors()->has('body'))->toBeTrue();
    expect($validator->errors()->has('tags'))->toBeTrue();
});
