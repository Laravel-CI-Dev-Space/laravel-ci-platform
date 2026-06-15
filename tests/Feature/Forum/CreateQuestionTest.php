<?php

declare(strict_types=1);

namespace Tests\Feature\Forum;

use App\Models\Question;
use App\Models\Tag;
use App\Models\User;

it('lets a member create a question with tags', function () {
    $member = makeMember();
    $tag    = Tag::create(['name' => 'Test Eloquent', 'slug' => 'test-eloquent', 'scope' => 'forum']);

    $response = $this->actingAs($member)->post(route('forum.questions.store'), [
        'title' => 'Comment optimiser une requête Eloquent N+1 ?',
        'body'  => 'Voici le contexte détaillé de mon problème de performance avec Eloquent et les relations.',
        'tags'  => [$tag->id],
    ]);

    $question = Question::first();

    $response->assertRedirect(route('forum.show', $question->slug));
    expect($question->user_id)->toBe($member->id);
    expect($question->tags->pluck('id'))->toContain($tag->id);
    expect($tag->refresh()->usage_count)->toBe(1);
});

it('rejects question creation from a user without the permission', function () {
    $user = User::factory()->create();
    \App\Models\Profile::create(['user_id' => $user->id]);
    $tag = Tag::create(['name' => 'Test Eloquent 2', 'slug' => 'test-eloquent-2', 'scope' => 'forum']);

    $response = $this->actingAs($user)->post(route('forum.questions.store'), [
        'title' => 'Comment optimiser une requête Eloquent N+1 ?',
        'body'  => 'Voici le contexte détaillé de mon problème de performance avec Eloquent et les relations.',
        'tags'  => [$tag->id],
    ]);

    $response->assertForbidden();
    expect(Question::count())->toBe(0);
});

it('validates the question fields', function () {
    $member = makeMember();

    $response = $this->actingAs($member)->post(route('forum.questions.store'), [
        'title' => 'Trop',
        'body'  => 'Trop court',
        'tags'  => [],
    ]);

    $response->assertSessionHasErrors(['title', 'body', 'tags']);
    expect(Question::count())->toBe(0);
});
