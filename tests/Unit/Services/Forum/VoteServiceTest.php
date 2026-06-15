<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Forum;

use App\Models\Question;
use App\Models\User;
use App\Services\Forum\VoteService;
use Tests\TestCase;

uses(TestCase::class);

it('upvotes a question and increments its score', function () {
    $user     = User::factory()->create();
    $question = Question::factory()->create(['votes_score' => 0]);
    $service  = new VoteService;

    $result = $service->vote($user, $question, 1);

    expect($result['score'])->toBe(1);
    expect($result['userVote'])->toBe(1);
    expect($service->getUserVote($user, $question))->toBe(1);
});

it('toggles off an upvote when voting the same value again', function () {
    $user     = User::factory()->create();
    $question = Question::factory()->create(['votes_score' => 0]);
    $service  = new VoteService;

    $service->vote($user, $question, 1);
    $result = $service->vote($user, $question, 1);

    expect($result['score'])->toBe(0);
    expect($result['userVote'])->toBeNull();
    expect($service->getUserVote($user, $question))->toBeNull();
});

it('switches from an upvote to a downvote', function () {
    $user     = User::factory()->create();
    $question = Question::factory()->create(['votes_score' => 0]);
    $service  = new VoteService;

    $service->vote($user, $question, 1);
    $result = $service->vote($user, $question, -1);

    expect($result['score'])->toBe(-1);
    expect($result['userVote'])->toBe(-1);
    expect($service->getUserVote($user, $question))->toBe(-1);
});
