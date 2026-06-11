<?php

namespace App\Mail;

use App\Models\CompanyRegistrationRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CompanyRegistrationRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly CompanyRegistrationRequest $request,
        public readonly string $reason,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Votre demande d'inscription — Laravel CI",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.company-registration-rejected',
        );
    }
}
