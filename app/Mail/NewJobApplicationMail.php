<?php

namespace App\Mail;

use App\Models\CompanyAccount;
use App\Models\JobApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewJobApplicationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly CompanyAccount $account,
        public readonly JobApplication $application,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Nouvelle candidature pour {$this->application->jobOffer->title} — Laravel CI",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.new-job-application',
        );
    }
}
