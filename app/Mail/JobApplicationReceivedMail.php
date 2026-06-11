<?php

namespace App\Mail;

use App\Models\JobApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class JobApplicationReceivedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly JobApplication $application,
    ) {}

    public function envelope(): Envelope
    {
        $title = $this->application->jobOffer->title;

        return new Envelope(
            subject: "Nouvelle candidature — {$title}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.job-application-received',
        );
    }
}
