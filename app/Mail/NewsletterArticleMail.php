<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Article;
use App\Models\NewsletterSubscriber;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class NewsletterArticleMail extends Mailable
{
    public function __construct(
        public readonly Article $article,
        public readonly NewsletterSubscriber $subscriber,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[Laravel CI] ' . $this->article->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.newsletter.article',
        );
    }
}
