<?php

namespace Tests\Feature\Staff;

use App\Enums\Role;
use App\Enums\TicketStatus;
use App\Models\ReturnTicket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TicketRefundTest extends TestCase
{
    use RefreshDatabase;

    public function test_refund_route_is_forbidden_for_non_manajemen_roles(): void
    {
        Storage::fake('public');

        $cs = User::factory()->create(['email' => 'cs@tokokita.com', 'role' => Role::CustomerService]);
        $gudang = User::factory()->create(['email' => 'gudang@tokokita.com', 'role' => Role::Gudang]);
        $ticket = ReturnTicket::factory()->create(['status' => TicketStatus::RefundDiproses]);

        $payload = [
            'refund_method' => 'Transfer Bank',
            'refund_proof' => UploadedFile::fake()->image('bukti.jpg'),
        ];

        $this->actingAs($cs)->post(route('staff.tickets.refund', $ticket), $payload)->assertForbidden();
        $this->actingAs($gudang)->post(route('staff.tickets.refund', $ticket), $payload)->assertForbidden();
        $this->assertSame(TicketStatus::RefundDiproses, $ticket->fresh()->status);
    }

    public function test_refund_requires_proof_file(): void
    {
        $manajemen = User::factory()->create(['email' => 'finance@tokokita.com', 'role' => Role::Manajemen]);
        $ticket = ReturnTicket::factory()->create(['status' => TicketStatus::RefundDiproses]);

        $this->actingAs($manajemen)
            ->from(route('staff.tickets.show', $ticket))
            ->post(route('staff.tickets.refund', $ticket), ['refund_method' => 'Transfer Bank'])
            ->assertSessionHasErrors('refund_proof');

        $fresh = $ticket->fresh();
        $this->assertSame(TicketStatus::RefundDiproses, $fresh->status);
        $this->assertNull($fresh->refund_proof_path);
    }

    public function test_refund_requires_method(): void
    {
        Storage::fake('public');

        $manajemen = User::factory()->create(['email' => 'finance@tokokita.com', 'role' => Role::Manajemen]);
        $ticket = ReturnTicket::factory()->create(['status' => TicketStatus::RefundDiproses]);

        $this->actingAs($manajemen)
            ->post(route('staff.tickets.refund', $ticket), [
                'refund_proof' => UploadedFile::fake()->image('bukti.jpg'),
            ])
            ->assertSessionHasErrors('refund_method');
    }

    public function test_manajemen_can_complete_refund_and_ticket_becomes_selesai(): void
    {
        Storage::fake('public');

        $manajemen = User::factory()->create(['email' => 'finance@tokokita.com', 'role' => Role::Manajemen]);
        $ticket = ReturnTicket::factory()->create(['status' => TicketStatus::RefundDiproses]);

        $file = UploadedFile::fake()->image('bukti-transfer.jpg');

        $response = $this->actingAs($manajemen)->post(route('staff.tickets.refund', $ticket), [
            'refund_method' => 'Transfer Bank',
            'refund_proof' => $file,
            'note' => 'Refund dikirim ke rekening pelanggan',
        ]);

        $response->assertRedirect(route('staff.tickets.show', $ticket));
        $response->assertSessionHas('success');

        $fresh = $ticket->fresh();
        $this->assertSame(TicketStatus::Selesai, $fresh->status);
        $this->assertSame('Transfer Bank', $fresh->refund_method);
        $this->assertNotNull($fresh->refund_proof_path);

        // File tersimpan di disk public pada folder refunds/{ticket_number}.
        $this->assertStringStartsWith('refunds/'.$ticket->ticket_number.'/', $fresh->refund_proof_path);
        Storage::disk('public')->assertExists($fresh->refund_proof_path);

        $this->assertDatabaseHas('ticket_status_histories', [
            'return_ticket_id' => $ticket->id,
            'to_status' => 'Selesai',
        ]);
    }

    public function test_admin_can_complete_refund(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create(['email' => 'admin@tokokita.com', 'role' => Role::Admin]);
        $ticket = ReturnTicket::factory()->create(['status' => TicketStatus::RefundDiproses]);

        $this->actingAs($admin)->post(route('staff.tickets.refund', $ticket), [
            'refund_method' => 'E-Wallet',
            'refund_proof' => UploadedFile::fake()->image('bukti.png'),
        ])->assertRedirect(route('staff.tickets.show', $ticket));

        $this->assertSame(TicketStatus::Selesai, $ticket->fresh()->status);
    }

    public function test_refund_on_wrong_status_is_rejected_and_file_is_cleaned_up(): void
    {
        Storage::fake('public');

        $manajemen = User::factory()->create(['email' => 'finance@tokokita.com', 'role' => Role::Manajemen]);
        $ticket = ReturnTicket::factory()->create(['status' => TicketStatus::MenungguBarang]);

        $this->actingAs($manajemen)
            ->post(route('staff.tickets.refund', $ticket), [
                'refund_method' => 'Transfer Bank',
                'refund_proof' => UploadedFile::fake()->image('bukti.jpg'),
            ])
            ->assertSessionHas('error');

        $fresh = $ticket->fresh();
        $this->assertSame(TicketStatus::MenungguBarang, $fresh->status);
        $this->assertNull($fresh->refund_proof_path);
        $this->assertSame([], Storage::disk('public')->files('refunds/'.$ticket->ticket_number));
    }
}