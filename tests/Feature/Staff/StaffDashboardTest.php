<?php

namespace Tests\Feature\Staff;

use App\Enums\Role;
use App\Enums\TicketStatus;
use App\Models\ReturnTicket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaffDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function staffUser(Role $role, string $email): User
    {
        return User::factory()->create(['email' => $email, 'role' => $role]);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('staff.dashboard'))->assertRedirect(route('login'));
    }

    public function test_dashboard_shows_statistic_cards_for_cs(): void
    {
        $cs = $this->staffUser(Role::CustomerService, 'cs@tokokita.com');

        ReturnTicket::factory()->count(2)->create(['status' => TicketStatus::Diajukan]);
        ReturnTicket::factory()->create(['status' => TicketStatus::Diverifikasi]);

        $response = $this->actingAs($cs)->get(route('staff.dashboard'));

        $response->assertOk();
        $response->assertViewHas('totalTickets', 3);
        $response->assertViewHas('todayTickets', 3);
        $response->assertSee('Total Tiket');
        $response->assertSee('Tiket Hari Ini');
    }

    public function test_dashboard_lists_tickets_relevant_to_cs_role(): void
    {
        $cs = $this->staffUser(Role::CustomerService, 'cs@tokokita.com');

        $diajukan = ReturnTicket::factory()->create(['status' => TicketStatus::Diajukan]);
        $diverifikasi = ReturnTicket::factory()->create(['status' => TicketStatus::Diverifikasi]);
        $disetujui = ReturnTicket::factory()->create(['status' => TicketStatus::Disetujui]);
        $menunggu = ReturnTicket::factory()->create(['status' => TicketStatus::MenungguBarang]);
        $selesai = ReturnTicket::factory()->create(['status' => TicketStatus::Selesai]);

        $response = $this->actingAs($cs)->get(route('staff.dashboard'));

        $numbers = $response->viewData('tickets')->pluck('ticket_number');

        $this->assertTrue($numbers->contains($diajukan->ticket_number));
        $this->assertTrue($numbers->contains($diverifikasi->ticket_number));
        $this->assertTrue($numbers->contains($disetujui->ticket_number));
        $this->assertFalse($numbers->contains($menunggu->ticket_number));
        $this->assertFalse($numbers->contains($selesai->ticket_number));
    }

    public function test_dashboard_lists_warehouse_stage_tickets_for_gudang(): void
    {
        $gudang = $this->staffUser(Role::Gudang, 'gudang@tokokita.com');

        $menunggu = ReturnTicket::factory()->create(['status' => TicketStatus::MenungguBarang]);
        $diterima = ReturnTicket::factory()->create(['status' => TicketStatus::BarangDiterima]);
        $periksa = ReturnTicket::factory()->create(['status' => TicketStatus::PemeriksaanGudang]);
        $diajukan = ReturnTicket::factory()->create(['status' => TicketStatus::Diajukan]);

        $response = $this->actingAs($gudang)->get(route('staff.dashboard'));

        $numbers = $response->viewData('tickets')->pluck('ticket_number');

        $this->assertTrue($numbers->contains($menunggu->ticket_number));
        $this->assertTrue($numbers->contains($diterima->ticket_number));
        $this->assertTrue($numbers->contains($periksa->ticket_number));
        $this->assertFalse($numbers->contains($diajukan->ticket_number));
    }

    public function test_dashboard_lists_refund_tickets_for_manajemen_and_all_for_admin(): void
    {
        $refundTicket = ReturnTicket::factory()->create(['status' => TicketStatus::RefundDiproses]);
        $diajukan = ReturnTicket::factory()->create(['status' => TicketStatus::Diajukan]);

        $manajemen = $this->staffUser(Role::Manajemen, 'finance@tokokita.com');
        $response = $this->actingAs($manajemen)->get(route('staff.dashboard'));
        $numbers = $response->viewData('tickets')->pluck('ticket_number');
        $this->assertTrue($numbers->contains($refundTicket->ticket_number));
        $this->assertFalse($numbers->contains($diajukan->ticket_number));

        $admin = $this->staffUser(Role::Admin, 'admin@tokokita.com');
        $response = $this->actingAs($admin)->get(route('staff.dashboard'));
        $numbers = $response->viewData('tickets')->pluck('ticket_number');
        $this->assertTrue($numbers->contains($refundTicket->ticket_number));
        $this->assertTrue($numbers->contains($diajukan->ticket_number));
    }
}