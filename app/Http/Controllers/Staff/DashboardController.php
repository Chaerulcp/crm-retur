<?php

namespace App\Http\Controllers\Staff;

use App\Enums\Role;
use App\Enums\TicketStatus;
use App\Http\Controllers\Controller;
use App\Models\ReturnTicket;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Daftar status non-terminal untuk kartu statistik "status aktif".
     *
     * @var array<int, TicketStatus>
     */
    public const ACTIVE_STATUSES = [
        TicketStatus::Diajukan,
        TicketStatus::Diverifikasi,
        TicketStatus::Disetujui,
        TicketStatus::MenungguBarang,
        TicketStatus::BarangDiterima,
        TicketStatus::PemeriksaanGudang,
        TicketStatus::RefundDiproses,
    ];

    /**
     * Dasbor staf: kartu statistik + daftar tiket yang relevan dengan peran.
     */
    public function __invoke(): View
    {
        $user = auth()->user();

        $perStatus = ReturnTicket::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $activeStatusCounts = collect(self::ACTIVE_STATUSES)
            ->map(fn (TicketStatus $status) => [
                'status' => $status,
                'total' => (int) ($perStatus[$status->value] ?? 0),
            ])
            ->all();

        $tickets = $this->relevantTicketsQuery($user)
            ->with(['customer', 'product', 'assignee'])
            ->latest()
            ->limit(10)
            ->get();

        return view('staff.dashboard', [
            'totalTickets' => ReturnTicket::query()->count(),
            'todayTickets' => ReturnTicket::query()->whereDate('created_at', today())->count(),
            'activeTotal' => collect($activeStatusCounts)->sum('total'),
            'activeStatusCounts' => $activeStatusCounts,
            'tickets' => $tickets,
        ]);
    }

    /**
     * Batasi daftar tiket dasbor sesuai peran pengguna.
     * CS: Diajukan/Diverifikasi/Disetujui; Gudang: Menunggu Barang s.d. Pemeriksaan;
     * Manajemen: Refund Diproses; Admin: semua tiket.
     */
    protected function relevantTicketsQuery($user): Builder
    {
        $statuses = match (true) {
            $user->role === Role::CustomerService => [
                TicketStatus::Diajukan,
                TicketStatus::Diverifikasi,
                TicketStatus::Disetujui,
            ],
            $user->role === Role::Gudang => [
                TicketStatus::MenungguBarang,
                TicketStatus::BarangDiterima,
                TicketStatus::PemeriksaanGudang,
            ],
            $user->role === Role::Manajemen => [
                TicketStatus::RefundDiproses,
            ],
            default => [],
        };

        $query = ReturnTicket::query();

        if ($statuses !== []) {
            $query->whereIn('status', $statuses);
        }

        return $query;
    }
}