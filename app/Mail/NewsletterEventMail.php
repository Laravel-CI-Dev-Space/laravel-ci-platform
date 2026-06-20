<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Event;
use App\Models\NewsletterSubscriber;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class NewsletterEventMail extends Mailable
{
    public function __construct(
        public readonly Event $event,
        public readonly NewsletterSubscriber $subscriber,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[Laravel CI] ' . $this->event->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.newsletter.event',
        );
    }
}
