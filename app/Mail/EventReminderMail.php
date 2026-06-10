<?php

namespace App\Mail;

use App\Enums\Events\EventReminderType;
use App\Models\Event;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EventReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly User $user,
        public readonly Event $event,
        public readonly EventReminderType $reminderType,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Rappel — {$this->event->title}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.event-reminder',
        );
    }
}
