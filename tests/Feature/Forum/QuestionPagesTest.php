<?php

declare(strict_types=1);

namespace Tests\Feature\Forum;

use App\Models\Answer;
use App\Models\Question;

it('lists published questions on the forum index', function () {
    $question = Question::factory()->create(['title' => 'Comment utiliser Eloquent ?']);
    Question::factory()->hidden()->create(['title' => 'Question cachée']);

    $response = $this->get(route('forum.index'));

    $response->assertOk();
    $response->assertSee($question->title);
    $response->assertDontSee('Question cachée');
});

it('shows a published question with its answers', function () {
    $question = Question::factory()->create(['title' => 'Pourquoi utiliser des Form Requests ?']);
    Answer::factory()->for($question)->create([
        'body'      => 'Pour valider proprement les entrées utilisateur.',
        'body_html' => '<p>Pour valider proprement les entrées utilisateur.</p>',
    ]);

    $response = $this->get(route('forum.show', $question->slug));

    $response->assertOk();
    $response->assertSee($question->title);
    $response->assertSee('Pour valider proprement les entrées utilisateur.', false);
});
