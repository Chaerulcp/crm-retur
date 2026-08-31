<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TicketStatus;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ReturnTicket;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AnalyticsController extends Controller
{
    /**
     * Dasbor analitik: kartu ringkasan + grafik Chart.js.
     */
    public function __invoke(): View
    {
        $totalTickets = ReturnTicket::count();
        $completedTotal = ReturnTicket::where('status', TicketStatus::Selesai)->count();
        $rejectedTotal = ReturnTicket::where('status', TicketStatus::Ditolak)->count();
        $refundProcessedTotal = ReturnTicket::where('status', TicketStatus::RefundDiproses)->count();

        [$dailyLabels, $dailyTotals] = $this->ticketsPerDay(30);
        $topProducts = Product::query()
            ->has('returnTickets')
            ->withCount('returnTickets')
            ->orderByDesc('return_tickets_count')
            ->orderBy('name')
            ->limit(5)
            ->get();

        $statusCounts = ReturnTicket::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $topReasons = ReturnTicket::query()
            ->selectRaw('reason, count(*) as total')
            ->groupBy('reason')
            ->orderByDesc('total')
            ->orderBy('reason')
            ->limit(5)
            ->get();

        $statusLabels = [];
        $statusTotals = [];

        foreach (TicketStatus::cases() as $status) {
            $total = (int) ($statusCounts[$status->value] ?? 0);

            if ($total > 0) {
                $statusLabels[] = $status->label();
                $statusTotals[] = $total;
            }
        }

        return view('admin.analytics', [
            'totalTickets' => $totalTickets,
            'completedTotal' => $completedTotal,
            'rejectedTotal' => $rejectedTotal,
            'refundProcessedTotal' => $refundProcessedTotal,
            'dailyLabels' => $dailyLabels,
            'dailyTotals' => $dailyTotals,
            'topProductLabels' => $topProducts->pluck('name')->all(),
            'topProductTotals' => $topProducts->pluck('return_tickets_count')->all(),
            'topReasonLabels' => $topReasons->pluck('reason')->all(),
            'topReasonTotals' => $topReasons->pluck('total')->map(fn ($total) => (int) $total)->all(),
            'statusLabels' => $statusLabels,
            'statusTotals' => $statusTotals,
        ]);
    }

    /**
     * Jumlah tiket per hari selama N hari terakhir (hari tanpa tiket dihitung 0).
     *
     * @return array{0: array<int, string>, 1: array<int, int>}
     */
    private function ticketsPerDay(int $days): array
    {
        $start = now()->subDays($days - 1)->startOfDay();

        $perDay = ReturnTicket::query()
            ->where('created_at', '>=', $start)
            ->selectRaw('date(created_at) as day, count(*) as total')
            ->groupBy(DB::raw('date(created_at)'))
            ->pluck('total', 'day');

        $labels = [];
        $totals = [];

        for ($date = $start->copy(); $date->lte(now()); $date = $date->addDay()) {
            $key = $date->format('Y-m-d');
            $labels[] = $key;
            $totals[] = (int) ($perDay[$key] ?? 0);
        }

        return [$labels, $totals];
    }
}