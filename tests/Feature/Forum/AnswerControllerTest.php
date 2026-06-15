<?php

declare(strict_types=1);

use App\Models\Question;

it('stores a new answer and redirects to the question page', function () {
    $user     = makeMember();
    $question = Question::factory()->create();

    $response = $this->actingAs($user)->post(route('forum.answers.store', $question), [
        'body' => str_repeat('Voici une réponse détaillée à cette question. ', 2),
    ]);

    $response->assertRedirect(route('forum.show', $question->slug));
    $response->assertSessionHas('success');
    $this->assertDatabaseHas('answers', [
        'question_id' => $question->id,
        'user_id'     => $user->id,
    ]);
});

it('rejects an answer that is too short', function () {
    $user     = makeMember();
    $question = Question::factory()->create();

    $response = $this->actingAs($user)->post(route('forum.answers.store', $question), [
        'body' => 'Trop court',
    ]);

    $response->assertSessionHasErrors('body');
});
