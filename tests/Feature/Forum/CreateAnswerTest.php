<?php

declare(strict_types=1);

namespace Tests\Feature\Forum;

use App\Models\Answer;
use App\Models\Question;
use App\Models\User;

it('lets a member answer a question', function () {
    $member   = makeMember();
    $question = Question::factory()->create(['answers_count' => 0]);

    $response = $this->actingAs($member)->post(route('forum.answers.store', $question), [
        'body' => 'Voici une réponse détaillée et utile pour résoudre ce problème précis.',
    ]);

    $response->assertRedirect(route('forum.show', $question->slug));

    $answer = Answer::first();
    expect($answer->user_id)->toBe($member->id);
    expect($answer->question_id)->toBe($question->id);
    expect($question->refresh()->answers_count)->toBe(1);
});

it('rejects an answer from a user without the permission', function () {
    $user = User::factory()->create();
    \App\Models\Profile::create(['user_id' => $user->id]);
    $question = Question::factory()->create();

    $response = $this->actingAs($user)->post(route('forum.answers.store', $question), [
        'body' => 'Voici une réponse détaillée et utile pour résoudre ce problème précis.',
    ]);

    $response->assertForbidden();
    expect(Answer::count())->toBe(0);
});

it('validates the answer body', function () {
    $member   = makeMember();
    $question = Question::factory()->create();

    $response = $this->actingAs($member)->post(route('forum.answers.store', $question), [
        'body' => 'Trop court',
    ]);

    $response->assertSessionHasErrors(['body']);
    expect(Answer::count())->toBe(0);
});
