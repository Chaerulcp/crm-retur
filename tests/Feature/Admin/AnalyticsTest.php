<?php

namespace Tests\Feature\Admin;

use App\Enums\Role;
use App\Enums\TicketStatus;
use App\Models\Product;
use App\Models\ReturnTicket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalyticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_manajemen_can_access_analytics(): void
    {
        $manajemen = User::factory()->create(['role' => Role::Manajemen]);

        $response = $this->actingAs($manajemen)->get(route('admin.analytics'));

        $response->assertOk();
        $response->assertSee('Analitik Retur');
        $response->assertSee('Total Tiket');
        $response->assertSee('Refund Diproses');
        $response->assertSee('https://cdn.jsdelivr.net/npm/chart.js', false);
    }

    public function test_admin_can_access_analytics(): void
    {
        $admin = User::factory()->create(['role' => Role::Admin]);

        $this->actingAs($admin)->get(route('admin.analytics'))->assertOk();
    }

    public function test_summary_cards_reflect_ticket_counts(): void
    {
        $admin = User::factory()->create(['role' => Role::Admin]);

        ReturnTicket::factory()->count(2)->create(['status' => TicketStatus::Diajukan]);
        ReturnTicket::factory()->count(3)->create(['status' => TicketStatus::Selesai]);
        ReturnTicket::factory()->create(['status' => TicketStatus::Ditolak]);
        ReturnTicket::factory()->count(2)->create(['status' => TicketStatus::RefundDiproses]);

        $response = $this->actingAs($admin)->get(route('admin.analytics'));

        $response->assertViewHas('totalTickets', 8);
        $response->assertViewHas('completedTotal', 3);
        $response->assertViewHas('rejectedTotal', 1);
        $response->assertViewHas('refundProcessedTotal', 2);
    }

    public function test_daily_chart_covers_30_days_with_zero_filled(): void
    {
        $admin = User::factory()->create(['role' => Role::Admin]);

        ReturnTicket::factory()->count(2)->create(); // hari ini
        ReturnTicket::factory()->create(['created_at' => now()->subDays(5)]);
        ReturnTicket::factory()->create(['created_at' => now()->subDays(40)]); // di luar rentang

        $response = $this->actingAs($admin)->get(route('admin.analytics'));

        $labels = $response->viewData('dailyLabels');
        $totals = $response->viewData('dailyTotals');

        $this->assertCount(30, $labels);
        $this->assertSame(now()->subDays(29)->format('Y-m-d'), $labels[0]);
        $this->assertSame(now()->format('Y-m-d'), $labels[29]);
        $this->assertSame(3, array_sum($totals));
        $this->assertSame(2, $totals[29]); // dua tiket dibuat hari ini
    }

    public function test_top_products_lists_most_returned_products(): void
    {
        $admin = User::factory()->create(['role' => Role::Admin]);

        $heavy = Product::factory()->create(['name' => 'Produk Paling Retur']);
        $medium = Product::factory()->create(['name' => 'Produk Sedang']);
        $light = Product::factory()->create(['name' => 'Produk Ringan']);
        Product::factory()->create(['name' => 'Tanpa Retur']);

        ReturnTicket::factory()->count(3)->create(['product_id' => $heavy->id]);
        ReturnTicket::factory()->count(2)->create(['product_id' => $medium->id]);
        ReturnTicket::factory()->create(['product_id' => $light->id]);

        $response = $this->actingAs($admin)->get(route('admin.analytics'));

        $this->assertSame(
            ['Produk Paling Retur', 'Produk Sedang', 'Produk Ringan'],
            $response->viewData('topProductLabels'),
        );
        $this->assertSame([3, 2, 1], $response->viewData('topProductTotals'));
    }

    public function test_status_distribution_groups_tickets_by_status(): void
    {
        $admin = User::factory()->create(['role' => Role::Admin]);

        ReturnTicket::factory()->count(2)->create(['status' => TicketStatus::Diajukan]);
        ReturnTicket::factory()->create(['status' => TicketStatus::Selesai]);

        $response = $this->actingAs($admin)->get(route('admin.analytics'));

        $labels = $response->viewData('statusLabels');
        $totals = $response->viewData('statusTotals');

        $diajukanIndex = array_search(TicketStatus::Diajukan->label(), $labels, true);
        $selesaiIndex = array_search(TicketStatus::Selesai->label(), $labels, true);

        $this->assertNotFalse($diajukanIndex);
        $this->assertSame(2, $totals[$diajukanIndex]);
        $this->assertNotFalse($selesaiIndex);
        $this->assertSame(1, $totals[$selesaiIndex]);
    }
}