<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LeadAdminAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  array<string, mixed>  $payload
     */
    public function __construct(public array $payload)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: sprintf('Yeni %s geldi', mb_strtolower((string) $this->payload['type_label'])),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.leads.admin-alert',
            with: ['payload' => $this->payload],
        );
    }
}
