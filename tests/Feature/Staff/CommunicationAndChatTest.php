<?php

namespace Tests\Feature\Staff;

use App\Enums\Role;
use App\Enums\SenderType;
use App\Enums\TicketStatus;
use App\Models\ReturnTicket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommunicationAndChatTest extends TestCase
{
    use RefreshDatabase;

    protected User $cs;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cs = User::factory()->create([
            'email' => 'cs@tokokita.com',
            'role' => Role::CustomerService,
        ]);
    }

    public function test_staff_can_add_internal_communication(): void
    {
        $ticket = ReturnTicket::factory()->create(['status' => TicketStatus::Diajukan]);

        $response = $this->actingAs($this->cs)->post(route('staff.tickets.communicate', $ticket), [
            'message' => 'Sudah dicek, menunggu kelengkapan foto.',
            'is_internal' => '1',
        ]);

        $response->assertRedirect(route('staff.tickets.show', $ticket));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('ticket_communications', [
            'return_ticket_id' => $ticket->id,
            'sender_id' => $this->cs->id,
            'sender_type' => SenderType::Staf->value,
            'message' => 'Sudah dicek, menunggu kelengkapan foto.',
            'is_internal' => true,
        ]);
    }

    public function test_communication_defaults_to_public_when_checkbox_unchecked(): void
    {
        $ticket = ReturnTicket::factory()->create(['status' => TicketStatus::Diajukan]);

        $this->actingAs($this->cs)->post(route('staff.tickets.communicate', $ticket), [
            'message' => 'Halo, retur Anda sedang kami proses.',
        ])->assertRedirect(route('staff.tickets.show', $ticket));

        $this->assertDatabaseHas('ticket_communications', [
            'return_ticket_id' => $ticket->id,
            'message' => 'Halo, retur Anda sedang kami proses.',
            'is_internal' => false,
        ]);
    }

    public function test_communication_requires_message(): void
    {
        $ticket = ReturnTicket::factory()->create(['status' => TicketStatus::Diajukan]);

        $this->actingAs($this->cs)
            ->post(route('staff.tickets.communicate', $ticket), ['message' => ''])
            ->assertSessionHasErrors('message');
    }

    public function test_chat_toggle_flips_chat_active_state(): void
    {
        $ticket = ReturnTicket::factory()->create(['chat_active' => false]);

        $this->actingAs($this->cs)
            ->post(route('staff.tickets.chat-toggle', $ticket))
            ->assertSessionHas('success');

        $this->assertTrue($ticket->fresh()->chat_active);

        $this->actingAs($this->cs)->post(route('staff.tickets.chat-toggle', $ticket));

        $this->assertFalse($ticket->fresh()->chat_active);
    }
}