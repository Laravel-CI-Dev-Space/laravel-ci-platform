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

class EventConfirmationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly User $user,
        public readonly EventRegistration $registration,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Inscription confirmée : {$this->registration->event->title} - Laravel CI",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.event-confirmation',
        );
    }
}
