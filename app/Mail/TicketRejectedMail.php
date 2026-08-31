<?php

namespace App\Mail;

use App\Models\ReturnTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketRejectedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public ReturnTicket $ticket,
        public ?string $note = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Permintaan Retur {$this->ticket->ticket_number} Belum Dapat Disetujui",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.ticket-rejected',
        );
    }
}
