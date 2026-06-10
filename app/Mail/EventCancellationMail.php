<?php

namespace App\Mail;

use App\Models\Event;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EventCancellationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly User $user,
        public readonly Event $event,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Annulation d'inscription — {$this->event->title}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.event-cancellation',
        );
    }
}
