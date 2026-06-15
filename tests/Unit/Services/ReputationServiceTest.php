<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Enums\QuestionStatus;
use App\Models\Answer;
use App\Models\Question;
use App\Models\User;
use App\Services\ReputationService;
use Tests\TestCase;

uses(TestCase::class);

it('calculates points from published questions and answers', function () {
    $user = User::factory()->create();

    Question::factory()->for($user)->create(['status' => QuestionStatus::Published]);
    Answer::factory()->for($user)->create(['is_accepted' => true, 'votes_score' => 3]);

    $service = app(ReputationService::class);
    $points  = $service->calculatePoints($user);

    // 5 (question) + 10 (answer) + 20 (accepted) + 2*3 (votes) = 41
    expect($points)->toBe(41);
});

it('determines the lowest grade for a new member', function () {
    $user = User::factory()->create();
    $user->assignRole('member');

    $service = app(ReputationService::class);
    $grade   = $service->determineGrade($user, 0);

    expect($grade->name)->toBe('Recrue');
});

it('always grants the highest grade to a super-admin', function () {
    $user = User::factory()->create();
    $user->assignRole('super-admin');

    $service = app(ReputationService::class);
    $grade   = $service->determineGrade($user, 0);

    expect($grade->name)->toBe('Grand Maître');
});

it('recalculates and persists points and grade on the profile', function () {
    $user = User::factory()->create();
    Question::factory()->for($user)->create(['status' => QuestionStatus::Published]);

    $profile = app(ReputationService::class)->recalculate($user);

    expect($profile->points)->toBe(5);
    expect($profile->grade->name)->toBe('Recrue');
    expect($user->profile()->first()->points)->toBe(5);
});
