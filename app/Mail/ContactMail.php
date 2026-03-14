<?php

namespace App\Mail;

use App\Services\Mail\ResolvedMailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly ResolvedMailTemplate $template,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->template->subject,
        );
    }

    public function content(): Content
    {
        // When resolved via Blade the bodyHtml is already rendered HTML.
        // When resolved via DB the bodyHtml is stored HTML.
        // In both cases we pass it directly as htmlString.
        return new Content(
            htmlString: $this->template->bodyHtml,
            text: null,
        );
    }
}

