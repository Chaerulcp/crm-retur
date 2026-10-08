<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="eyebrow">Operasi</p>
                <h1 class="mt-1 font-display text-2xl font-bold text-ink">Tiket Retur</h1>
            </div>
        </div>
    </x-slot>

    @if (session('success'))
        <div class="flash-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="flash-error">{{ session('error') }}</div>
    @endif

    {{-- Filter status + pencarian --}}
    <form method="GET" action="{{ route('staff.tickets.index') }}" class="card flex flex-col gap-4 p-4 sm:p-5 lg:flex-row lg:items-end">
        <div class="flex-1">
            <label for="search" class="label">Pencarian</label>
            <input type="text" name="search" id="search" value="{{ $search }}"
                   placeholder="Nomor tiket, nama pelanggan, atau produk..."
                   class="input mt-1.5">
        </div>
        <div class="lg:w-60">
            <label for="status" class="label">Status</label>
            <select name="status" id="status" class="input mt-1.5">
                <option value="">Semua Status</option>
                @foreach ($statuses as $status)
                    <option value="{{ $status->value }}" @selected($selectedStatus === $status->value)>{{ $status->label() }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="btn-primary">Terapkan</button>
            <a href="{{ route('staff.tickets.index') }}" class="btn-secondary">Reset</a>
        </div>
    </form>

    {{-- Tabel tiket --}}
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="th">Nomor Tiket</th>
                        <th class="th">Pelanggan</th>
                        <th class="th">Produk</th>
                        <th class="th">Diajukan</th>
                        <th class="th">Status</th>
                        <th class="th">Petugas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($tickets as $ticket)
                        <tr class="transition hover:bg-brand-50/40">
                            <td class="td">
                                <a href="{{ route('staff.tickets.show', $ticket) }}"
                                   class="font-mono text-sm font-semibold text-brand-700 hover:text-brand-800 hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 rounded">
                                    {{ $ticket->ticket_number }}
                                </a>
                            </td>
                            <td class="td font-medium text-slate-700">{{ $ticket->customer->name }}</td>
                            <td class="td text-slate-600">{{ $ticket->product->name }}</td>
                            <td class="td font-mono text-xs text-slate-500">{{ $ticket->created_at->format('d M Y') }}</td>
                            <td class="td"><x-status-badge :status="$ticket->status" /></td>
                            <td class="td text-slate-500">{{ $ticket->assignee?->name ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="td px-6 py-14 text-center">
                                <p class="text-sm text-slate-400">Tidak ada tiket yang cocok dengan filter/pencarian.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($tickets->hasPages())
            <div class="border-t border-slate-100 bg-slate-50/60 px-5 py-3.5">
                {{ $tickets->links() }}
            </div>
        @endif
    </div>
</x-app-layout>