<?php

declare(strict_types=1);

use App\Models\Question;
use App\Models\Tag;

it('lists published questions with tags', function () {
    Question::factory()->create();

    $response = $this->get(route('forum.index'));

    $response->assertOk();
    $response->assertViewIs('web.forum.index');
    $response->assertViewHas('questions');
    $response->assertViewHas('tags');
});

it('shows the question creation form to an authorized member', function () {
    $user = makeMember();

    $response = $this->actingAs($user)->get(route('forum.ask'));

    $response->assertOk();
    $response->assertViewIs('web.forum.ask');
});

it('stores a new question and redirects to its page', function () {
    $user = makeMember();
    $tag  = Tag::create(['name' => 'Laravel Forum Test', 'slug' => 'laravel-forum-test', 'scope' => 'forum']);

    $response = $this->actingAs($user)->post(route('forum.questions.store'), [
        'title' => 'Comment configurer correctement Laravel Sail ?',
        'body'  => str_repeat('Ceci est le corps détaillé de ma question. ', 3),
        'tags'  => [$tag->id],
    ]);

    $question = Question::where('title', 'Comment configurer correctement Laravel Sail ?')->first();

    expect($question)->not->toBeNull();
    $response->assertRedirect(route('forum.show', $question->slug));
    $response->assertSessionHas('success');
});

it('shows a question and increments its view count', function () {
    $question = Question::factory()->create(['views_count' => 0]);

    $response = $this->get(route('forum.show', $question->slug));

    $response->assertOk();
    $response->assertViewIs('web.forum.show');
    expect($question->refresh()->views_count)->toBe(1);
});

it('allows the owner to edit their question within 48h', function () {
    $user     = makeMember();
    $question = Question::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->get(route('forum.edit', $question));

    $response->assertOk();
    $response->assertViewIs('web.forum.edit');
});

it('denies editing a question owned by another member', function () {
    $owner = makeMember();
    $other = makeMember();
    $question = Question::factory()->create(['user_id' => $owner->id]);

    $response = $this->actingAs($other)->get(route('forum.edit', $question));

    $response->assertForbidden();
});

it('allows the owner to delete their question', function () {
    $user     = makeMember();
    $question = Question::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->delete(route('forum.destroy', $question));

    $response->assertRedirect(route('forum.index'));
    $response->assertSessionHas('success');
    $this->assertSoftDeleted('questions', ['id' => $question->id]);
});

it('denies deleting a question owned by another member', function () {
    $owner = makeMember();
    $other = makeMember();
    $question = Question::factory()->create(['user_id' => $owner->id]);

    $response = $this->actingAs($other)->delete(route('forum.destroy', $question));

    $response->assertForbidden();
});
