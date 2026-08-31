<x-app-layout>
    <x-slot name="header">
        <p class="eyebrow">Administrasi</p>
        <h1 class="mt-1 font-display text-2xl font-bold text-ink">Analitik Retur</h1>
    </x-slot>

    {{-- Kartu ringkasan --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="card p-5">
            <p class="text-sm font-medium text-slate-500">Total Tiket</p>
            <p class="mt-2 font-mono text-3xl font-semibold text-ink">{{ $totalTickets }}</p>
        </div>
        <div class="card p-5">
            <p class="text-sm font-medium text-slate-500">Selesai</p>
            <p class="mt-2 font-mono text-3xl font-semibold text-emerald-600">{{ $completedTotal }}</p>
        </div>
        <div class="card p-5">
            <p class="text-sm font-medium text-slate-500">Ditolak</p>
            <p class="mt-2 font-mono text-3xl font-semibold text-red-600">{{ $rejectedTotal }}</p>
        </div>
        <div class="card p-5">
            <p class="text-sm font-medium text-slate-500">Refund Diproses</p>
            <p class="mt-2 font-mono text-3xl font-semibold text-indigo-600">{{ $refundProcessedTotal }}</p>
        </div>
    </div>

    {{-- Garis: tiket per hari (30 hari terakhir) --}}
    <div class="card p-5 sm:p-6">
        <h2 class="text-sm font-semibold text-ink">Tiket per Hari (30 Hari Terakhir)</h2>
        <div class="mt-4">
            <canvas id="daily-tickets-chart" height="90"></canvas>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        {{-- Bar: 5 produk terbanyak diretur --}}
        <div class="card p-5 sm:p-6">
            <h2 class="text-sm font-semibold text-ink">5 Produk Terbanyak Diretur</h2>
            <div class="mt-4">
                <canvas id="top-products-chart"></canvas>
            </div>
        </div>

        {{-- Doughnut: distribusi status --}}
        <div class="card p-5 sm:p-6">
            <h2 class="text-sm font-semibold text-ink">Distribusi Status Tiket</h2>
            <div class="mt-4">
                <canvas id="status-distribution-chart"></canvas>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const dailyLabels = {!! json_encode($dailyLabels) !!};
        const dailyTotals = {!! json_encode($dailyTotals) !!};
        const topProductLabels = {!! json_encode($topProductLabels) !!};
        const topProductTotals = {!! json_encode($topProductTotals) !!};
        const statusLabels = {!! json_encode($statusLabels) !!};
        const statusTotals = {!! json_encode($statusTotals) !!};

        new Chart(document.getElementById('daily-tickets-chart'), {
            type: 'line',
            data: {
                labels: dailyLabels,
                datasets: [{
                    label: 'Jumlah Tiket',
                    data: dailyTotals,
                    borderColor: '#22646A',
                    backgroundColor: 'rgba(34, 100, 106, 0.10)',
                    pointBackgroundColor: '#22646A',
                    tension: 0.3,
                    fill: true,
                }],
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
            },
        });

        new Chart(document.getElementById('top-products-chart'), {
            type: 'bar',
            data: {
                labels: topProductLabels,
                datasets: [{
                    label: 'Jumlah Retur',
                    data: topProductTotals,
                    backgroundColor: '#3B7E82',
                    borderRadius: 6,
                }],
            },
            options: {
                responsive: true,
                scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
            },
        });

        new Chart(document.getElementById('status-distribution-chart'), {
            type: 'doughnut',
            data: {
                labels: statusLabels,
                datasets: [{
                    data: statusTotals,
                    backgroundColor: [
                        '#94a3b8', '#38bdf8', '#3b82f6', '#ef4444', '#f59e0b',
                        '#fb923c', '#8b5cf6', '#6366f1', '#10b981',
                    ],
                }],
            },
            options: { responsive: true },
        });
    </script>
</x-app-layout>