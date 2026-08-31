<?php

namespace Tests\Feature\Chat;

use App\Enums\Role;
use App\Models\ChatMessage;
use App\Models\ReturnTicket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChatMessagesTest extends TestCase
{
    use RefreshDatabase;

    protected User $cs;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cs = User::factory()->create(['role' => Role::CustomerService]);
    }

    private function seedMessages(ReturnTicket $ticket): void
    {
        $ticket->chatMessages()->create([
            'sender_type' => ChatMessage::SENDER_CUSTOMER,
            'sender_name' => $ticket->customer->name,
            'message' => 'Halo, status retur saya bagaimana?',
        ]);
        $ticket->chatMessages()->create([
            'sender_type' => ChatMessage::SENDER_STAFF,
            'sender_id' => $this->cs->id,
            'sender_name' => $this->cs->name,
            'message' => 'Sedang kami proses, mohon ditunggu.',
        ]);
    }

    public function test_staff_can_fetch_messages_even_when_chat_inactive(): void
    {
        $ticket = ReturnTicket::factory()->create(['chat_active' => false]);
        $this->seedMessages($ticket);

        $response = $this->actingAs($this->cs)
            ->getJson(route('chat.messages', $ticket));

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [['id', 'sender_type', 'sender_name', 'message', 'created_at']],
                'ticket' => ['chat_active'],
            ])
            ->assertJsonPath('ticket.chat_active', false)
            ->assertJsonPath('data.0.message', 'Halo, status retur saya bagaimana?')
            ->assertJsonPath('data.0.sender_type', ChatMessage::SENDER_CUSTOMER)
            ->assertJsonPath('data.1.message', 'Sedang kami proses, mohon ditunggu.')
            ->assertJsonPath('data.1.sender_type', ChatMessage::SENDER_STAFF)
            ->assertJsonPath('data.1.sender_name', $this->cs->name);
    }

    public function test_messages_are_returned_ascending_and_filtered_by_after_id(): void
    {
        $ticket = ReturnTicket::factory()->create(['chat_active' => true]);
        $this->seedMessages($ticket);

        $firstId = $ticket->chatMessages()->orderBy('id')->value('id');

        $response = $this->actingAs($this->cs)
            ->getJson(route('chat.messages', ['ticket' => $ticket, 'after_id' => $firstId]));

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.message', 'Sedang kami proses, mohon ditunggu.');

        // ID terbesar selalu berada di urutan terakhir (ascending).
        $this->assertGreaterThan($firstId, $response->json('data.0.id'));
    }

    public function test_customer_with_valid_token_can_fetch_messages(): void
    {
        $ticket = ReturnTicket::factory()->create(['chat_active' => true]);
        $this->seedMessages($ticket);

        $response = $this->getJson(route('chat.messages', [
            'ticket' => $ticket,
            'token' => $ticket->tracking_token,
        ]));

        $response->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('ticket.chat_active', true);
    }

    public function test_customer_with_wrong_token_is_rejected(): void
    {
        $ticket = ReturnTicket::factory()->create(['chat_active' => true]);
        $this->seedMessages($ticket);

        $response = $this->getJson(route('chat.messages', [
            'ticket' => $ticket,
            'token' => 'token-salah',
        ]));

        $response->assertForbidden()->assertJsonPath('message', 'Token akses tidak valid.');
    }

    public function test_customer_without_token_is_rejected(): void
    {
        $ticket = ReturnTicket::factory()->create(['chat_active' => true]);

        $this->getJson(route('chat.messages', $ticket))->assertForbidden();
    }

    public function test_customer_is_rejected_when_chat_inactive(): void
    {
        $ticket = ReturnTicket::factory()->create(['chat_active' => false]);
        $this->seedMessages($ticket);

        $response = $this->getJson(route('chat.messages', [
            'ticket' => $ticket,
            'token' => $ticket->tracking_token,
        ]));

        $response->assertForbidden()
            ->assertJsonPath('message', 'Live chat untuk tiket ini sedang tidak aktif.');
    }
}
