<?php

namespace Tests\Feature\Portal;

use App\Enums\SenderType;
use App\Enums\TicketStatus;
use App\Models\ReturnTicket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortalTrackingTest extends TestCase
{
    use RefreshDatabase;

    public function test_halaman_lacak_menampilkan_form_pencarian(): void
    {
        $this->get(route('portal.tracking'))
            ->assertOk()
            ->assertSee('Lacak Retur');
    }

    public function test_pencarian_nomor_valid_mengarahkan_ke_halaman_tiket(): void
    {
        $ticket = ReturnTicket::factory()->create();

        $this->get(route('portal.tracking', ['nomor' => $ticket->ticket_number]))
            ->assertRedirect(route('portal.tracking.show', ['ticket_number' => $ticket->ticket_number]));
    }

    public function test_pencarian_nomor_tidak_ditemukan_memberikan_kesalahan(): void
    {
        $this->get(route('portal.tracking', ['nomor' => 'RET-20260101-9999']))
            ->assertSessionHasErrors('nomor');
    }

    public function test_halaman_pelacakan_menampilkan_status_bukti_dan_riwayat(): void
    {
        $ticket = ReturnTicket::factory()->create();

        $ticket->communications()->create([
            'sender_type' => SenderType::Staf,
            'message' => 'Permintaan Anda sedang kami verifikasi.',
            'is_internal' => false,
        ]);
        $ticket->communications()->create([
            'sender_type' => SenderType::Staf,
            'message' => 'Catatan internal staf yang rahasia.',
            'is_internal' => true,
        ]);
        $ticket->statusHistories()->create([
            'from_status' => null,
            'to_status' => TicketStatus::Diajukan->value,
            'note' => 'Tiket diajukan oleh pelanggan.',
        ]);

        $response = $this->get(route('portal.tracking.show', ['ticket_number' => $ticket->ticket_number]));

        $response->assertOk()
            ->assertSee($ticket->ticket_number)
            ->assertSee(TicketStatus::Diajukan->label())
            // Badge status memakai kelas Tailwind dari enum.
            ->assertSee(TicketStatus::Diajukan->badgeClass(), false)
            // Riwayat komunikasi publik tampil, catatan internal tidak.
            ->assertSee('Permintaan Anda sedang kami verifikasi.')
            ->assertDontSee('Catatan internal staf yang rahasia.')
            // Timeline status tampil.
            ->assertSee('Tiket diajukan oleh pelanggan.')
            // Widget live chat ikut dirender beserta token untuk pelanggan.
            ->assertSee('Live Chat')
            ->assertSee('data-chat-token="'.$ticket->tracking_token.'"', false);
    }

    public function test_akses_dengan_nomor_tidak_valid_gagal(): void
    {
        $this->get(route('portal.tracking.show', ['ticket_number' => 'RET-20260101-0000']))
            ->assertNotFound();
    }
}
