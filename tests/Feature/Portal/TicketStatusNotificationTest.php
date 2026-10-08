<?php

namespace Tests\Feature\Portal;

use App\Enums\Role;
use App\Enums\TicketStatus;
use App\Events\TicketStatusChanged;
use App\Listeners\SendTicketStatusNotification;
use App\Mail\RefundCompletedMail;
use App\Mail\TicketRejectedMail;
use App\Mail\TicketStatusUpdatedMail;
use App\Models\ReturnTicket;
use App\Models\User;
use App\Services\TicketWorkflow;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class TicketStatusNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_notifikasi_diantrekan_saat_workflow_mengubah_status(): void
    {
        Mail::fake();

        $ticket = ReturnTicket::factory()->create();
        $cs = User::factory()->create(['role' => Role::CustomerService]);

        app(TicketWorkflow::class)->transition($ticket, TicketStatus::Diverifikasi, $cs, 'Data lengkap.');

        Mail::assertQueued(TicketStatusUpdatedMail::class, function (TicketStatusUpdatedMail $mail) use ($ticket): bool {
            return $mail->hasTo($ticket->customer->email)
                && $mail->note === 'Data lengkap.'
                && str_contains($mail->envelope()->subject, $ticket->ticket_number);
        });
    }

    public function test_status_ditolak_mengirim_email_penolakan(): void
    {
        Mail::fake();

        $ticket = ReturnTicket::factory()->create(['status' => TicketStatus::Ditolak]);

        event(new TicketStatusChanged($ticket, null, 'Tidak sesuai kebijakan retur.'));

        Mail::assertQueued(
            TicketRejectedMail::class,
            fn (TicketRejectedMail $mail): bool => $mail->hasTo($ticket->customer->email)
                && $mail->note === 'Tidak sesuai kebijakan retur.',
        );
    }

    public function test_status_selesai_mengirim_email_refund_selesai(): void
    {
        Mail::fake();

        $ticket = ReturnTicket::factory()->create([
            'status' => TicketStatus::Selesai,
            'refund_method' => 'Transfer Bank',
        ]);

        event(new TicketStatusChanged($ticket));

        Mail::assertQueued(
            RefundCompletedMail::class,
            fn (RefundCompletedMail $mail): bool => $mail->hasTo($ticket->customer->email),
        );
    }

    public function test_status_diajukan_tidak_memicu_notifikasi(): void
    {
        Mail::fake();

        $ticket = ReturnTicket::factory()->create();

        event(new TicketStatusChanged($ticket));

        Mail::assertNothingQueued();
    }

    public function test_pemilihan_mailable_sesuai_status_baru(): void
    {
        $ticket = ReturnTicket::factory()->make();

        $ticket->status = TicketStatus::Diverifikasi;
        $this->assertInstanceOf(TicketStatusUpdatedMail::class, SendTicketStatusNotification::mailableFor($ticket));

        $ticket->status = TicketStatus::Ditolak;
        $this->assertInstanceOf(TicketRejectedMail::class, SendTicketStatusNotification::mailableFor($ticket));

        $ticket->status = TicketStatus::Selesai;
        $this->assertInstanceOf(RefundCompletedMail::class, SendTicketStatusNotification::mailableFor($ticket));

        $ticket->status = TicketStatus::Diajukan;
        $this->assertNull(SendTicketStatusNotification::mailableFor($ticket));
    }

    public function test_email_notifikasi_dapat_dirender(): void
    {
        $ticket = ReturnTicket::factory()->create(['status' => TicketStatus::Disetujui]);

        $mail = new TicketStatusUpdatedMail($ticket, 'Catatan uji coba.');

        $html = $mail->render();

        $this->assertStringContainsString($ticket->ticket_number, $html);
        $this->assertStringContainsString('Catatan uji coba.', $html);
    }
}
