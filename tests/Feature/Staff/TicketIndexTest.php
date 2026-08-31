<?php

namespace Tests\Feature\Staff;

use App\Enums\Role;
use App\Enums\TicketStatus;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ReturnTicket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketIndexTest extends TestCase
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

    public function test_index_lists_tickets_with_status_badge(): void
    {
        $ticket = ReturnTicket::factory()->create(['status' => TicketStatus::Diajukan]);

        $response = $this->actingAs($this->cs)->get(route('staff.tickets.index'));

        $response->assertOk();
        $response->assertSee($ticket->ticket_number);
        $response->assertSee($ticket->customer->name);
        $response->assertSee($ticket->product->name);
        $response->assertSee('Diajukan');
        $response->assertSee(TicketStatus::Diajukan->badgeClass());
    }

    public function test_index_can_filter_by_status(): void
    {
        $diajukan = ReturnTicket::factory()->create(['status' => TicketStatus::Diajukan]);
        $selesai = ReturnTicket::factory()->create(['status' => TicketStatus::Selesai]);

        $response = $this->actingAs($this->cs)->get(route('staff.tickets.index', ['status' => 'Diajukan']));

        $numbers = $response->viewData('tickets')->pluck('ticket_number');
        $this->assertTrue($numbers->contains($diajukan->ticket_number));
        $this->assertFalse($numbers->contains($selesai->ticket_number));
    }

    public function test_index_rejects_invalid_status_filter(): void
    {
        $this->actingAs($this->cs)
            ->get(route('staff.tickets.index', ['status' => 'BukanStatus']))
            ->assertSessionHasErrors('status');
    }

    public function test_index_can_search_by_ticket_number_customer_and_product(): void
    {
        $customer = Customer::factory()->create(['name' => 'Budi Santoso']);
        $product = Product::factory()->create(['name' => 'Keyboard Mekanik K87']);

        $byCustomer = ReturnTicket::factory()->create(['customer_id' => $customer->id]);
        $byProduct = ReturnTicket::factory()->create(['product_id' => $product->id]);
        $other = ReturnTicket::factory()->create();

        // Pencarian nama pelanggan.
        $response = $this->actingAs($this->cs)->get(route('staff.tickets.index', ['search' => 'Budi']));
        $numbers = $response->viewData('tickets')->pluck('ticket_number');
        $this->assertTrue($numbers->contains($byCustomer->ticket_number));
        $this->assertFalse($numbers->contains($other->ticket_number));

        // Pencarian nama produk.
        $response = $this->actingAs($this->cs)->get(route('staff.tickets.index', ['search' => 'Keyboard Mekanik']));
        $numbers = $response->viewData('tickets')->pluck('ticket_number');
        $this->assertTrue($numbers->contains($byProduct->ticket_number));
        $this->assertFalse($numbers->contains($other->ticket_number));

        // Pencarian nomor tiket.
        $response = $this->actingAs($this->cs)->get(route('staff.tickets.index', ['search' => $other->ticket_number]));
        $numbers = $response->viewData('tickets')->pluck('ticket_number');
        $this->assertTrue($numbers->contains($other->ticket_number));
        $this->assertCount(1, $numbers);
    }

    public function test_index_paginates_15_tickets_per_page(): void
    {
        ReturnTicket::factory()->count(16)->create();

        $pageOne = $this->actingAs($this->cs)->get(route('staff.tickets.index'));
        $pageOne->assertOk();
        $this->assertSame(15, $pageOne->viewData('tickets')->count());
        $this->assertSame(16, $pageOne->viewData('tickets')->total());

        $pageTwo = $this->actingAs($this->cs)->get(route('staff.tickets.index', ['page' => 2]));
        $this->assertSame(1, $pageTwo->viewData('tickets')->count());
    }
}