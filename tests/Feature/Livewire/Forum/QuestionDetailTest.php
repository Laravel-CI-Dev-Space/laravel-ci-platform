<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\Forum;

use App\Livewire\Forum\QuestionDetail;
use App\Models\Answer;
use App\Models\Question;
use App\Models\User;
use Livewire\Livewire;

it('renders a question by its slug', function () {
    $question = Question::factory()->create();

    Livewire::test(QuestionDetail::class, ['slug' => $question->slug])
        ->assertSee($question->user->name);
});

it('toggles the answer form', function () {
    $question = Question::factory()->create();

    Livewire::test(QuestionDetail::class, ['slug' => $question->slug])
        ->assertSet('showAnswerForm', true)
        ->call('toggleAnswerForm')
        ->assertSet('showAnswerForm', false);
});

it('lets the question author accept an answer', function () {
    $author   = User::factory()->create();
    $question = Question::factory()->for($author, 'user')->create();
    $answer   = Answer::factory()->for($question)->create();

    Livewire::actingAs($author)
        ->test(QuestionDetail::class, ['slug' => $question->slug])
        ->call('acceptAnswer', $answer->id);

    expect($answer->refresh()->is_accepted)->toBeTrue();
    expect($question->refresh()->accepted_answer_id)->toBe($answer->id);
});

it('forbids a non-author from accepting an answer', function () {
    $author   = User::factory()->create();
    $other    = User::factory()->create();
    $question = Question::factory()->for($author, 'user')->create();
    $answer   = Answer::factory()->for($question)->create();

    Livewire::actingAs($other)
        ->test(QuestionDetail::class, ['slug' => $question->slug])
        ->call('acceptAnswer', $answer->id);

    expect($answer->refresh()->is_accepted)->toBeFalse();
});
