<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Forum;

use App\Models\Question;
use App\Models\User;
use App\Services\Forum\CommentService;
use Tests\TestCase;

uses(TestCase::class);

it('creates a comment and increments the commentable counter', function () {
    $user     = User::factory()->create();
    $question = Question::factory()->create(['comments_count' => 0]);
    $service  = app(CommentService::class);

    $comment = $service->createComment($user, $question, ['body' => 'Merci pour cette question !']);

    expect($comment->user_id)->toBe($user->id);
    expect($comment->commentable_id)->toBe($question->id);
    expect($comment->commentable_type)->toBe($question->getMorphClass());
    expect($question->refresh()->comments_count)->toBe(1);
});

it('paginates visible top-level comments with their replies', function () {
    $user     = User::factory()->create();
    $question = Question::factory()->create(['comments_count' => 0]);
    $service  = app(CommentService::class);

    $parent = $service->createComment($user, $question, ['body' => 'Commentaire principal']);
    $service->createComment($user, $question, ['body' => 'Une réponse', 'parent_id' => $parent->id]);
    $hidden = $service->createComment($user, $question, ['body' => 'Commentaire masqué']);
    $hidden->update(['is_hidden' => true]);

    $comments = $service->getComments($question);

    expect($comments->total())->toBe(1);
    expect($comments->first()->id)->toBe($parent->id);
    expect($comments->first()->replies)->toHaveCount(1);
});
