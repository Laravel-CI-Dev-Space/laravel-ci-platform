<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\Forum;

use App\Livewire\Forum\VoteButtons;
use App\Models\Question;
use App\Models\User;
use App\Models\Vote;
use Livewire\Livewire;

it('lets an authenticated user upvote a question', function () {
    $user     = User::factory()->create();
    $question = Question::factory()->create(['votes_score' => 0]);

    Livewire::actingAs($user)
        ->test(VoteButtons::class, ['model' => $question])
        ->call('upvote')
        ->assertSet('score', 1)
        ->assertSet('userVote', 1);

    expect($question->refresh()->votes_score)->toBe(1);
    expect(Vote::where('user_id', $user->id)->where('votable_id', $question->id)->first()->value)->toBe(1);
});

it('toggles an existing upvote off when clicked again', function () {
    $user     = User::factory()->create();
    $question = Question::factory()->create(['votes_score' => 0]);

    $component = Livewire::actingAs($user)
        ->test(VoteButtons::class, ['model' => $question])
        ->call('upvote');

    $component->call('upvote')
        ->assertSet('score', 0)
        ->assertSet('userVote', null);

    expect($question->refresh()->votes_score)->toBe(0);
});

it('redirects guests to login when attempting to vote', function () {
    $question = Question::factory()->create(['votes_score' => 0]);

    Livewire::test(VoteButtons::class, ['model' => $question])
        ->call('upvote')
        ->assertRedirect(route('login'));
});
