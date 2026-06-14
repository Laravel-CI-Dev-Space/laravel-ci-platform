<?php

namespace App\Mail;

use App\Models\JobOffer;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class JobAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly User $user,
        public readonly JobOffer $offer,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Nouvelle offre — {$this->offer->title}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.job-alert',
        );
    }
}
