<?php

namespace Tests\Feature\Staff;

use App\Enums\ItemCondition;
use App\Enums\Role;
use App\Enums\TicketStatus;
use App\Models\ReturnTicket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketTransitionTest extends TestCase
{
    use RefreshDatabase;

    protected function staffUser(Role $role): User
    {
        return User::factory()->create([
            'email' => strtolower($role->name).'-'.uniqid().'@tokokita.com',
            'role' => $role,
        ]);
    }

    public function test_cs_can_make_valid_transition_with_note(): void
    {
        $cs = User::factory()->create(['email' => 'cs@tokokita.com', 'role' => Role::CustomerService]);
        $ticket = ReturnTicket::factory()->create(['status' => TicketStatus::Diajukan]);

        $response = $this->actingAs($cs)->post(route('staff.tickets.transition', $ticket), [
            'status' => TicketStatus::Diverifikasi->value,
            'note' => 'Dokumen lengkap',
        ]);

        $response->assertRedirect(route('staff.tickets.show', $ticket));
        $response->assertSessionHas('success');

        $this->assertSame(TicketStatus::Diverifikasi, $ticket->fresh()->status);
        $this->assertSame($cs->id, $ticket->fresh()->assigned_to);

        $this->assertDatabaseHas('ticket_status_histories', [
            'return_ticket_id' => $ticket->id,
            'user_id' => $cs->id,
            'from_status' => 'Diajukan',
            'to_status' => 'Diverifikasi',
            'note' => 'Dokumen lengkap',
        ]);
    }

    public function test_invalid_transition_path_is_rejected_with_flash_error(): void
    {
        $cs = User::factory()->create(['email' => 'cs@tokokita.com', 'role' => Role::CustomerService]);
        $ticket = ReturnTicket::factory()->create(['status' => TicketStatus::Diajukan]);

        $response = $this->actingAs($cs)->post(route('staff.tickets.transition', $ticket), [
            'status' => TicketStatus::Selesai->value,
        ]);

        $response->assertSessionHas('error');
        $this->assertSame(TicketStatus::Diajukan, $ticket->fresh()->status);
    }

    public function test_transition_is_rejected_when_role_is_not_allowed(): void
    {
        $gudang = $this->staffUser(Role::Gudang);
        $ticket = ReturnTicket::factory()->create(['status' => TicketStatus::Diajukan]);

        // Gudang tidak boleh memverifikasi tiket (hanya CS/Admin).
        $response = $this->actingAs($gudang)->post(route('staff.tickets.transition', $ticket), [
            'status' => TicketStatus::Diverifikasi->value,
        ]);

        $response->assertSessionHas('error');
        $this->assertSame(TicketStatus::Diajukan, $ticket->fresh()->status);
    }

    public function test_invalid_status_value_fails_validation(): void
    {
        $cs = User::factory()->create(['email' => 'cs@tokokita.com', 'role' => Role::CustomerService]);
        $ticket = ReturnTicket::factory()->create(['status' => TicketStatus::Diajukan]);

        $this->actingAs($cs)
            ->from(route('staff.tickets.show', $ticket))
            ->post(route('staff.tickets.transition', $ticket), ['status' => 'Ngawur'])
            ->assertSessionHasErrors('status');

        $this->assertSame(TicketStatus::Diajukan, $ticket->fresh()->status);
    }

    public function test_gudang_verdict_layak_moves_ticket_to_refund_diproses(): void
    {
        $gudang = $this->staffUser(Role::Gudang);
        $ticket = ReturnTicket::factory()->create(['status' => TicketStatus::PemeriksaanGudang]);

        $response = $this->actingAs($gudang)->post(route('staff.tickets.condition', $ticket), [
            'condition' => ItemCondition::Layak->value,
            'note' => 'Barang sesuai dan berfungsi',
        ]);

        $response->assertRedirect(route('staff.tickets.show', $ticket));

        $fresh = $ticket->fresh();
        $this->assertSame(TicketStatus::RefundDiproses, $fresh->status);
        $this->assertSame(ItemCondition::Layak, $fresh->item_condition);
    }

    public function test_gudang_verdict_tidak_layak_moves_ticket_to_selesai(): void
    {
        $gudang = $this->staffUser(Role::Gudang);
        $ticket = ReturnTicket::factory()->create(['status' => TicketStatus::PemeriksaanGudang]);

        $this->actingAs($gudang)->post(route('staff.tickets.condition', $ticket), [
            'condition' => ItemCondition::TidakLayak->value,
        ])->assertRedirect(route('staff.tickets.show', $ticket));

        $fresh = $ticket->fresh();
        $this->assertSame(TicketStatus::Selesai, $fresh->status);
        $this->assertSame(ItemCondition::TidakLayak, $fresh->item_condition);
    }

    public function test_cs_cannot_submit_warehouse_verdict(): void
    {
        $cs = User::factory()->create(['email' => 'cs@tokokita.com', 'role' => Role::CustomerService]);
        $ticket = ReturnTicket::factory()->create(['status' => TicketStatus::PemeriksaanGudang]);

        $response = $this->actingAs($cs)->post(route('staff.tickets.condition', $ticket), [
            'condition' => ItemCondition::Layak->value,
        ]);

        $response->assertSessionHas('error');
        $this->assertSame(TicketStatus::PemeriksaanGudang, $ticket->fresh()->status);
    }

    public function test_condition_requires_valid_value(): void
    {
        $gudang = $this->staffUser(Role::Gudang);
        $ticket = ReturnTicket::factory()->create(['status' => TicketStatus::PemeriksaanGudang]);

        $this->actingAs($gudang)
            ->post(route('staff.tickets.condition', $ticket), ['condition' => 'Rusak Ringan'])
            ->assertSessionHasErrors('condition');
    }
}