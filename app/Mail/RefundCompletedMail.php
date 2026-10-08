<?php

namespace App\Mail;

use App\Models\ReturnTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RefundCompletedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public ReturnTicket $ticket) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Proses Retur {$this->ticket->ticket_number} Telah Selesai",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.refund-completed',
            with: [
                'trackingUrl' => route('portal.tracking.show', [
                    'ticket_number' => $this->ticket->ticket_number,
                    // Token disertakan sebagai query param agar widget live chat dapat dipakai.
                    'token' => $this->ticket->tracking_token,
                ]),
            ],
        );
    }
}
