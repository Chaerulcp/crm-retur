<?php

namespace Tests\Feature\Chat;

use App\Enums\Role;
use App\Models\ChatMessage;
use App\Models\ReturnTicket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChatStoreTest extends TestCase
{
    use RefreshDatabase;

    protected User $cs;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cs = User::factory()->create(['role' => Role::CustomerService]);
    }

    public function test_staff_can_send_message(): void
    {
        $ticket = ReturnTicket::factory()->create(['chat_active' => true]);

        $response = $this->actingAs($this->cs)
            ->postJson(route('chat.store', $ticket), ['message' => 'Halo, ada yang bisa dibantu?']);

        $response->assertCreated()
            ->assertJsonStructure(['data' => ['id', 'sender_type', 'sender_name', 'message', 'created_at']])
            ->assertJsonPath('data.sender_type', ChatMessage::SENDER_STAFF)
            ->assertJsonPath('data.sender_name', $this->cs->name)
            ->assertJsonPath('data.message', 'Halo, ada yang bisa dibantu?');

        $this->assertDatabaseHas('chat_messages', [
            'return_ticket_id' => $ticket->id,
            'sender_type' => ChatMessage::SENDER_STAFF,
            'sender_id' => $this->cs->id,
            'sender_name' => $this->cs->name,
            'message' => 'Halo, ada yang bisa dibantu?',
        ]);
    }

    public function test_staff_can_send_message_even_when_chat_inactive(): void
    {
        $ticket = ReturnTicket::factory()->create(['chat_active' => false]);

        $this->actingAs($this->cs)
            ->postJson(route('chat.store', $ticket), ['message' => 'Riwayat tetap bisa ditambah staf.'])
            ->assertCreated();

        $this->assertDatabaseCount('chat_messages', 1);
    }

    public function test_customer_with_valid_token_can_send_message(): void
    {
        $ticket = ReturnTicket::factory()->create(['chat_active' => true]);

        $response = $this->postJson(route('chat.store', $ticket), [
            'token' => $ticket->tracking_token,
            'message' => 'Terima kasih atas bantuannya.',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.sender_type', ChatMessage::SENDER_CUSTOMER)
            ->assertJsonPath('data.sender_name', $ticket->customer->name);

        $this->assertDatabaseHas('chat_messages', [
            'return_ticket_id' => $ticket->id,
            'sender_type' => ChatMessage::SENDER_CUSTOMER,
            'sender_id' => null,
            'sender_name' => $ticket->customer->name,
            'message' => 'Terima kasih atas bantuannya.',
        ]);
    }

    public function test_customer_with_wrong_token_cannot_send_message(): void
    {
        $ticket = ReturnTicket::factory()->create(['chat_active' => true]);

        $response = $this->postJson(route('chat.store', $ticket), [
            'token' => 'token-salah',
            'message' => 'Pesan dari penyusup.',
        ]);

        $response->assertForbidden()->assertJsonPath('message', 'Token akses tidak valid.');
        $this->assertDatabaseCount('chat_messages', 0);
    }

    public function test_customer_cannot_send_message_when_chat_inactive(): void
    {
        $ticket = ReturnTicket::factory()->create(['chat_active' => false]);

        $response = $this->postJson(route('chat.store', $ticket), [
            'token' => $ticket->tracking_token,
            'message' => 'Apakah masih ada orang?',
        ]);

        $response->assertForbidden()
            ->assertJsonPath('message', 'Live chat untuk tiket ini sedang tidak aktif.');
        $this->assertDatabaseCount('chat_messages', 0);
    }

    public function test_message_is_required(): void
    {
        $ticket = ReturnTicket::factory()->create(['chat_active' => true]);

        $this->actingAs($this->cs)
            ->postJson(route('chat.store', $ticket), ['message' => ''])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('message');
    }

    public function test_message_must_not_exceed_2000_characters(): void
    {
        $ticket = ReturnTicket::factory()->create(['chat_active' => true]);

        $this->actingAs($this->cs)
            ->postJson(route('chat.store', $ticket), ['message' => str_repeat('a', 2001)])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('message');

        $this->assertDatabaseCount('chat_messages', 0);
    }
}
