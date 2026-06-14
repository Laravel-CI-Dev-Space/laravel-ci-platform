<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\User;
use App\Services\Forum\MentionService;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    $this->mentionService = app(MentionService::class);
});

it('extracts multiple unique usernames from a text', function () {
    $usernames = $this->mentionService->extractUsernames('Salut @alice et @bob, merci @alice !');

    expect($usernames->all())->toBe(['alice', 'bob']);
});

it('limits extracted usernames to the maximum of 10', function () {
    $text = collect(range(1, 15))
        ->map(fn (int $i) => "@user{$i}")
        ->implode(' ');

    $usernames = $this->mentionService->extractUsernames($text);

    expect($usernames)->toHaveCount(10);
});

it('resolves mentioned users excluding the author and inactive accounts', function () {
    $author = User::factory()->create([
        'github_id'       => 'gh-author',
        'github_username' => 'author',
    ]);

    $alice = User::factory()->create([
        'github_id'       => 'gh-alice',
        'github_username' => 'alice',
        'is_active'       => true,
    ]);

    User::factory()->create([
        'github_id'       => 'gh-bob',
        'github_username' => 'bob',
        'is_active'       => false,
    ]);

    $resolved = $this->mentionService->resolveMentionedUsers('Salut @alice, @bob et @author', $author);

    expect($resolved->pluck('id')->all())->toBe([$alice->id]);
});

it('renders links for existing active users and leaves unknown mentions unchanged', function () {
    User::factory()->create([
        'github_id'       => 'gh-alice',
        'github_username' => 'alice',
        'is_active'       => true,
    ]);

    $rendered = $this->mentionService->renderMentions('Salut @alice et @inconnu !');

    expect($rendered)
        ->toContain('<a href="' . route('members.show', 'alice') . '" class="mention">@alice</a>')
        ->toContain('@inconnu');
});
