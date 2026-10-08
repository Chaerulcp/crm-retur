<?php

namespace App\Listeners;

use App\Enums\TicketStatus;
use App\Events\TicketStatusChanged;
use App\Mail\RefundCompletedMail;
use App\Mail\TicketRejectedMail;
use App\Mail\TicketStatusUpdatedMail;
use App\Models\ReturnTicket;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;

/**
 * Kirim notifikasi email ke pelanggan setiap kali status tiket berubah.
 *
 * Listener ini ter-discover otomatis oleh Laravel untuk event
 * App\Events\TicketStatusChanged (dilempar oleh TicketWorkflow::transition).
 *
 * Pemilihan template sesuai status baru:
 * - Ditolak              => TicketRejectedMail
 * - Selesai              => RefundCompletedMail
 * - Status lainnya       => TicketStatusUpdatedMail
 * - Diajukan             => dilewati (sudah ditangani TicketSubmittedMail saat pembuatan tiket)
 */
class SendTicketStatusNotification
{
    public function handle(TicketStatusChanged $event): void
    {
        $ticket = $event->ticket;

        if ($ticket->customer === null || blank($ticket->customer->email)) {
            return;
        }

        $mailable = self::mailableFor($ticket, $event->note);

        if ($mailable === null) {
            return;
        }

        Mail::to($ticket->customer->email)->queue($mailable);
    }

    /**
     * Pilih Mailable (template) sesuai status baru tiket.
     */
    public static function mailableFor(ReturnTicket $ticket, ?string $note = null): ?Mailable
    {
        return match ($ticket->status) {
            TicketStatus::Diajukan => null,
            TicketStatus::Ditolak => new TicketRejectedMail($ticket, $note),
            TicketStatus::Selesai => new RefundCompletedMail($ticket),
            default => new TicketStatusUpdatedMail($ticket, $note),
        };
    }
}
