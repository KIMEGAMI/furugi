<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class AdminBulkMail extends Mailable
{
    public function __construct(
        private readonly string $subjectLine,
        private readonly string $bodyText,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subjectLine,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin-bulk',
            with: [
                'body' => $this->bodyText,
            ],
        );
    }
}
