<?php

namespace App\Mail;

use App\Models\EventRegistration;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EventReminderMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @param  string  $type  '7d' ou '1d'
     */
    public function __construct(
        public readonly User $user,
        public readonly EventRegistration $registration,
        public readonly string $type,
    ) {}

    public function envelope(): Envelope
    {
        $title = $this->registration->event->title;

        $subject = $this->type === '7d'
            ? "Dans 7 jours : {$title} - Laravel CI"
            : "Demain : {$title} - Laravel CI";

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.event-reminder',
        );
    }
}
