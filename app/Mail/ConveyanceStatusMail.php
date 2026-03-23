<?php

namespace App\Mail;

use App\Models\Conveyance;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ConveyanceStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Conveyance $conveyance
    ) {}

    public function envelope(): Envelope
    {
        $status = ucfirst($this->conveyance->status);
        return new Envelope(
            subject: "Conveyance Claim {$status} — Investment CRM",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.conveyance-status',
        );
    }
}