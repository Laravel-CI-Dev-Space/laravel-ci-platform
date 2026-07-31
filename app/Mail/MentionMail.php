<?php

namespace App\Mail;

use App\Models\Answer;
use App\Models\Comment;
use App\Models\Question;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class MentionMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly User $mentionedUser,
        public readonly User $author,
        public readonly Model $source,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "{$this->author->name} vous a mentionné - Laravel CI",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.mention',
            with: [
                'context' => $this->context(),
                'excerpt' => $this->excerpt(),
                'url'     => $this->url(),
            ],
        );
    }

    /**
     * Décrit le contexte de la mention (question, réponse ou commentaire).
     */
    private function context(): string
    {
        return match (true) {
            $this->source instanceof Question => "dans la question « {$this->source->title} »",
            $this->source instanceof Answer   => "dans une réponse à « {$this->source->question->title} »",
            $this->source instanceof Comment  => 'dans un commentaire',
            default                           => '',
        };
    }

    /**
     * Extrait du texte mentionnant l'utilisateur.
     */
    private function excerpt(): string
    {
        $body = match (true) {
            $this->source instanceof Question => $this->source->body,
            $this->source instanceof Answer   => $this->source->body,
            $this->source instanceof Comment  => $this->source->body,
            default                           => '',
        };

        return Str::limit(strip_tags($body), 150);
    }

    /**
     * Génère l'URL vers le contenu mentionnant l'utilisateur.
     */
    private function url(): string
    {
        return match (true) {
            $this->source instanceof Question => route('forum.show', $this->source->slug),
            $this->source instanceof Answer   => route('forum.show', $this->source->question->slug) . '#answer-' . $this->source->id,
            $this->source instanceof Comment  => app(NotificationService::class)->resolveCommentUrl($this->source),
            default                           => route('home'),
        };
    }
}
