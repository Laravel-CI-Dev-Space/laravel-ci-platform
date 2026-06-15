<?php

declare(strict_types=1);

namespace Tests\Feature\Blog;

use App\Models\Article;

it('lists published articles on the blog index', function () {
    $published = Article::factory()->published()->create(['title' => 'Maîtriser les Form Requests']);
    $draft     = Article::factory()->create(['title' => 'Brouillon non publié']);

    $response = $this->get(route('blog.index'));

    $response->assertOk();
    $response->assertSee($published->title);
    $response->assertDontSee($draft->title);
});

it('shows a published article', function () {
    $article = Article::factory()->published()->create(['title' => 'Introduction à Livewire']);

    $response = $this->get(route('blog.show', $article->slug));

    $response->assertOk();
    $response->assertSee($article->title);
});

it('returns 404 for an unpublished article', function () {
    $article = Article::factory()->create(['title' => 'Brouillon privé']);

    $response = $this->get(route('blog.show', $article->slug));

    $response->assertNotFound();
});
