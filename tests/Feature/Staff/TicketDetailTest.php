<?php

namespace Tests\Feature\Staff;

use App\Enums\Role;
use App\Enums\SenderType;
use App\Enums\TicketStatus;
use App\Models\ReturnTicket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketDetailTest extends TestCase
{
    use RefreshDatabase;

    public function test_show_displays_detail_timeline_communications_evidences_and_chat_widget(): void
    {
        $cs = User::factory()->create(['email' => 'cs@tokokita.com', 'role' => Role::CustomerService]);

        $ticket = ReturnTicket::factory()->create(['status' => TicketStatus::Diverifikasi]);

        $ticket->statusHistories()->create([
            'user_id' => $cs->id,
            'from_status' => 'Diajukan',
            'to_status' => 'Diverifikasi',
            'note' => 'Data pelanggan valid',
        ]);

        $ticket->communications()->create([
            'sender_id' => $cs->id,
            'sender_type' => SenderType::Staf,
            'message' => 'Catatan internal verifikasi',
            'is_internal' => true,
        ]);

        (new \App\Models\TicketEvidence)
            ->setTable('ticket_evidences')
            ->forceFill([
                'return_ticket_id' => $ticket->id,
                'path' => 'evidences/foto-retur.jpg',
                'original_name' => 'foto-retur.jpg',
                'kind' => 'image',
                'size' => 1024,
            ])
            ->save();

        $response = $this->actingAs($cs)->get(route('staff.tickets.show', $ticket));

        $response->assertOk();
        $response->assertSee($ticket->ticket_number);
        $response->assertSee($ticket->customer->name);
        $response->assertSee($ticket->product->name);
        $response->assertSee($ticket->reason);

        // Timeline riwayat.
        $response->assertSee('Riwayat Status');
        $response->assertSee('Diverifikasi');
        $response->assertSee('Data pelanggan valid');

        // Komunikasi.
        $response->assertSee('Catatan internal verifikasi');
        $response->assertSee('Internal');

        // Bukti gambar.
        $response->assertSee('/storage/evidences/foto-retur.jpg');

        // Widget live chat ikut dirender.
        $response->assertSee('Live Chat');
    }

    public function test_show_renders_transition_buttons_for_available_transitions(): void
    {
        $cs = User::factory()->create(['email' => 'cs@tokokita.com', 'role' => Role::CustomerService]);
        $ticket = ReturnTicket::factory()->create(['status' => TicketStatus::Diajukan]);

        $response = $this->actingAs($cs)->get(route('staff.tickets.show', $ticket));

        $response->assertOk();
        // CS dapat memindahkan Diajukan -> Diverifikasi / Ditolak.
        $response->assertSee('Ubah ke Diverifikasi');
        $response->assertSee('Ubah ke Ditolak');
        $response->assertSee(route('staff.tickets.transition', $ticket));
    }
}