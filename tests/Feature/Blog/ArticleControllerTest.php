<?php

declare(strict_types=1);

use App\Models\Article;
use App\Models\Tag;

it('lists published articles with tags', function () {
    Article::factory()->published()->create();

    $response = $this->get(route('blog.index'));

    $response->assertOk();
    $response->assertViewIs('web.blog.index');
    $response->assertViewHas('articles');
    $response->assertViewHas('tags');
});

it('shows a published article and increments its view count', function () {
    $article = Article::factory()->published()->create(['views_count' => 0]);

    $response = $this->get(route('blog.show', $article->slug));

    $response->assertOk();
    $response->assertViewIs('web.blog.show');
    expect($article->refresh()->views_count)->toBe(1);
});

it('shows the article submission form to an authorized member', function () {
    $user = makeMember();

    $response = $this->actingAs($user)->get(route('blog.create'));

    $response->assertOk();
    $response->assertViewIs('web.blog.submit');
});

it('allows the owner to edit their article within 48h', function () {
    $user    = makeMember();
    $article = Article::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->get(route('blog.articles.edit', $article));

    $response->assertOk();
    $response->assertViewIs('web.blog.edit');
});

it('denies editing an article owned by another member', function () {
    $owner   = makeMember();
    $other   = makeMember();
    $article = Article::factory()->create(['user_id' => $owner->id]);

    $response = $this->actingAs($other)->get(route('blog.articles.edit', $article));

    $response->assertForbidden();
});

it('stores a new article as a draft', function () {
    $user = makeMember();
    $tag  = Tag::create(['name' => 'Eloquent Blog Test', 'slug' => 'eloquent-blog-test', 'scope' => 'blog']);

    $response = $this->actingAs($user)->post(route('blog.articles.store'), [
        'title' => 'Un guide complet sur les migrations Laravel',
        'body'  => str_repeat('Contenu détaillé de l\'article. ', 10),
        'level' => 'beginner',
        'tags'  => [$tag->id],
    ]);

    $response->assertRedirect(route('dashboard.member.articles'));
    $response->assertSessionHas('success');
    $this->assertDatabaseHas('articles', [
        'user_id' => $user->id,
        'title'   => 'Un guide complet sur les migrations Laravel',
        'status'  => 'draft',
    ]);
});

it('submits a draft article for review', function () {
    $user    = makeMember();
    $article = Article::factory()->create(['user_id' => $user->id, 'status' => 'draft']);

    $response = $this->actingAs($user)->post(route('blog.articles.submit', $article));

    $response->assertRedirect(route('dashboard.member.articles'));
    $response->assertSessionHas('success');
    expect($article->refresh()->status->value)->toBe('pending');
});

it('denies submitting an article owned by another member', function () {
    $owner   = makeMember();
    $other   = makeMember();
    $article = Article::factory()->create(['user_id' => $owner->id, 'status' => 'draft']);

    $response = $this->actingAs($other)->post(route('blog.articles.submit', $article));

    $response->assertForbidden();
});

it('rejects submitting an article that is not draft or rejected', function () {
    $user    = makeMember();
    $article = Article::factory()->published()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->post(route('blog.articles.submit', $article));

    $response->assertStatus(422);
});

it('allows the owner to delete their article', function () {
    $user    = makeMember();
    $article = Article::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->delete(route('blog.articles.destroy', $article));

    $response->assertRedirect(route('dashboard.member.articles'));
    $response->assertSessionHas('success');
    $this->assertSoftDeleted('articles', ['id' => $article->id]);
});

it('denies deleting an article owned by another member', function () {
    $owner   = makeMember();
    $other   = makeMember();
    $article = Article::factory()->create(['user_id' => $owner->id]);

    $response = $this->actingAs($other)->delete(route('blog.articles.destroy', $article));

    $response->assertForbidden();
});
