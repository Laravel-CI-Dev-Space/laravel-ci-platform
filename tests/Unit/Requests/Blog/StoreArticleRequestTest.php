<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\Blog;

use App\Http\Requests\Blog\StoreArticleRequest;
use App\Models\Tag;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

uses(TestCase::class);

it('authorizes an active member with the article create permission', function () {
    $user    = makeMember();
    $request = new StoreArticleRequest();
    $request->setUserResolver(fn () => $user);

    expect($request->authorize())->toBeTrue();
});

it('denies a guest from creating an article', function () {
    $request = new StoreArticleRequest();
    $request->setUserResolver(fn () => null);

    expect($request->authorize())->toBeFalse();
});

it('validates the required fields and minimum lengths', function () {
    $request = new StoreArticleRequest();

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

it('passes validation with valid data and an allowed tag', function () {
    $tag = Tag::create(['name' => 'Store Article Tag', 'slug' => 'store-article-tag', 'scope' => 'blog']);

    $request = new StoreArticleRequest();

    $validator = Validator::make([
        'title' => 'Comment structurer un projet Laravel de A à Z',
        'body'  => str_repeat('Voici un guide détaillé pour structurer votre projet Laravel. ', 5),
        'level' => 'beginner',
        'tags'  => [$tag->id],
    ], $request->rules(), $request->messages());

    expect($validator->fails())->toBeFalse();
});

it('rejects a tag that is not scoped for the blog', function () {
    $tag = Tag::create(['name' => 'Forum Only Tag', 'slug' => 'forum-only-tag', 'scope' => 'forum']);

    $request = new StoreArticleRequest();

    $validator = Validator::make([
        'title' => 'Comment structurer un projet Laravel de A à Z',
        'body'  => str_repeat('Voici un guide détaillé pour structurer votre projet Laravel. ', 5),
        'level' => 'beginner',
        'tags'  => [$tag->id],
    ], $request->rules(), $request->messages());

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('tags.0'))->toBeTrue();
});
