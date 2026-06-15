<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\Blog;

use App\Livewire\Blog\SubmitArticle;
use App\Models\Article;
use App\Models\Tag;
use App\Models\User;
use Livewire\Livewire;

it('lets an authenticated user save a draft article with tags', function () {
    $user = User::factory()->create();
    $tag  = Tag::create(['name' => 'Submit Article Tag', 'slug' => 'submit-article-tag', 'scope' => 'blog']);

    Livewire::actingAs($user)
        ->test(SubmitArticle::class)
        ->set('title', 'Comment structurer un projet Laravel de A à Z')
        ->set('body', str_repeat('Voici un guide détaillé pour structurer votre projet Laravel. ', 5))
        ->set('level', 'beginner')
        ->call('addTag', $tag->id)
        ->call('save')
        ->assertHasNoErrors();

    $article = Article::first();
    expect($article)->not->toBeNull();
    expect($article->user_id)->toBe($user->id);
    expect($article->tags->pluck('id'))->toContain($tag->id);
});

it('validates required fields before saving an article', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(SubmitArticle::class)
        ->set('title', 'Court')
        ->set('body', 'Court')
        ->call('save')
        ->assertHasErrors(['title', 'body', 'selectedTags']);
});

it('toggles preview mode', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(SubmitArticle::class)
        ->assertSet('preview', false)
        ->call('togglePreview')
        ->assertSet('preview', true);
});
