<?php

namespace App\Mail;

use App\Enums\TicketStatus;
use App\Models\ReturnTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketStatusUpdatedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public ReturnTicket $ticket,
        public ?string $note = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Pembaruan Retur {$this->ticket->ticket_number}: {$this->ticket->status->label()}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.ticket-status-updated',
            with: [
                'description' => $this->description(),
                'trackingUrl' => route('portal.tracking.show', [
                    'ticket_number' => $this->ticket->ticket_number,
                    // Token disertakan sebagai query param agar widget live chat dapat dipakai.
                    'token' => $this->ticket->tracking_token,
                ]),
            ],
        );
    }

    /**
     * Penjelasan singkat per status baru (dipilih oleh Listener).
     */
    public function description(): string
    {
        return match ($this->ticket->status) {
            TicketStatus::Diverifikasi => 'Permintaan Anda telah selesai diverifikasi oleh tim Customer Service kami dan sedang menunggu persetujuan.',
            TicketStatus::Disetujui => 'Selamat! Permintaan retur Anda disetujui. Silakan kirimkan barang retur Anda ke alamat gudang kami beserta nomor retur yang tertera.',
            TicketStatus::MenungguBarang => 'Kami sedang menunggu barang retur Anda tiba di gudang kami. Mohon pastikan barang dikemas dengan aman.',
            TicketStatus::BarangDiterima => 'Barang retur Anda telah kami terima di gudang dan akan segera masuk ke proses pemeriksaan.',
            TicketStatus::PemeriksaanGudang => 'Tim gudang kami sedang memeriksa kondisi barang retur Anda. Hasil pemeriksaan akan menentukan proses selanjutnya.',
            TicketStatus::RefundDiproses => 'Barang retur Anda memenuhi syarat. Pengembalian dana (refund) sedang diproses oleh tim keuangan kami.',
            default => 'Terdapat pembaruan pada proses retur Anda.',
        };
    }
}
