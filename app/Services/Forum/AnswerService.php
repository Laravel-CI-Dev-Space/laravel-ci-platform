<?php

declare(strict_types=1);

namespace App\Services\Forum;

use App\Models\Answer;
use App\Models\Question;
use App\Models\User;

class AnswerService
{
    public function __construct(
        private readonly MentionService $mentionService,
    ) {}

    /**
     * Crée une réponse à une question.
     */
    public function createAnswer(User $user, Question $question, array $data): Answer
    {
        $answer = Answer::create([
            'question_id' => $question->id,
            'user_id'     => $user->id,
            'body'        => $data['body'],
            'body_html'   => $this->mentionService->renderMentions($this->parseMarkdown($data['body'])),
        ]);

        $question->increment('answers_count');
        $question->update(['last_activity_at' => now()]);

        $this->mentionService->processMentions($answer->body, $user, $answer);

        return $answer;
    }

    /**
     * Marque une réponse comme solution acceptée.
     * Seul l'auteur de la question peut faire cette action.
     */
    public function acceptAnswer(User $user, Answer $answer): void
    {
        $question = $answer->question;

        abort_unless($question->isOwnedBy($user), 403, 'Seul l\'auteur de la question peut accepter une réponse.');

        if ($question->accepted_answer_id) {
            Answer::where('id', $question->accepted_answer_id)->update(['is_accepted' => false]);
        }

        $answer->update(['is_accepted' => true]);
        $question->update(['accepted_answer_id' => $answer->id]);
    }

    /**
     * Retire le statut de solution acceptée.
     */
    public function unacceptAnswer(User $user, Answer $answer): void
    {
        $question = $answer->question;

        abort_unless($question->isOwnedBy($user), 403, 'Seul l\'auteur de la question peut retirer une réponse acceptée.');

        $answer->update(['is_accepted' => false]);
        $question->update(['accepted_answer_id' => null]);
    }

    private function parseMarkdown(string $body): string
    {
        $paragraphs = array_filter(array_map('trim', explode("\n\n", $body)));

        if (empty($paragraphs)) {
            return '<p>' . nl2br(e($body)) . '</p>';
        }

        return implode('', array_map(
            static fn (string $p): string => '<p>' . nl2br(e($p)) . '</p>',
            $paragraphs,
        ));
    }
}
