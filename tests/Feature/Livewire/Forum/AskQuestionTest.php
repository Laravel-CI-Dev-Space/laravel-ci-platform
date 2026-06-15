<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\Forum;

use App\Livewire\Forum\AskQuestion;
use App\Models\Question;
use App\Models\Tag;
use App\Models\User;
use Livewire\Livewire;

it('lets an authenticated user post a question with tags', function () {
    $user = User::factory()->create();
    $tag  = Tag::create(['name' => 'Ask Question Tag', 'slug' => 'ask-question-tag', 'scope' => 'forum']);

    Livewire::actingAs($user)
        ->test(AskQuestion::class)
        ->set('title', 'Comment optimiser une requête Eloquent ?')
        ->set('body', 'Voici le contexte détaillé de mon problème de performance avec Eloquent.')
        ->call('addTag', $tag->id)
        ->call('save')
        ->assertHasNoErrors();

    $question = Question::first();
    expect($question)->not->toBeNull();
    expect($question->user_id)->toBe($user->id);
    expect($question->tags->pluck('id'))->toContain($tag->id);
});

it('validates required fields before posting a question', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(AskQuestion::class)
        ->set('title', 'Court')
        ->set('body', 'Court')
        ->call('save')
        ->assertHasErrors(['title', 'body', 'selectedTags']);
});

it('adds and removes tags from the selection', function () {
    $user = User::factory()->create();
    $tag  = Tag::create(['name' => 'Toggle Tag', 'slug' => 'toggle-tag', 'scope' => 'forum']);

    $component = Livewire::actingAs($user)
        ->test(AskQuestion::class)
        ->call('addTag', $tag->id);

    expect($component->get('selectedTags'))->toContain($tag->id);

    $component->call('removeTag', $tag->id);
    expect($component->get('selectedTags'))->not->toContain($tag->id);
});
