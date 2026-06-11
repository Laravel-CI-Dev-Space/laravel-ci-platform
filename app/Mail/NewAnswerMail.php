<?php

namespace App\Mail;

use App\Models\Answer;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewAnswerMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly User $user,
        public readonly Answer $answer,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nouvelle réponse à votre question — Laravel CI',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.new-answer',
        );
    }
}
