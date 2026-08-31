<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="eyebrow">Dasbor</p>
                <h1 class="mt-1 font-display text-2xl font-bold text-ink">Ringkasan Operasional</h1>
                <p class="mt-1 text-sm text-slate-500">Masuk sebagai {{ auth()->user()->name }} &middot; {{ auth()->user()->role->label() }}</p>
            </div>
            <a href="{{ route('staff.tickets.index') }}" class="btn-dark">
                Semua Tiket
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M3 10a.75.75 0 0 1 .75-.75h9.69L10.22 6.03a.75.75 0 1 1 1.06-1.06l4.5 4.5a.75.75 0 0 1 0 1.06l-4.5 4.5a.75.75 0 1 1-1.06-1.06l3.22-3.22H3.75A.75.75 0 0 1 3 10Z" clip-rule="evenodd"/></svg>
            </a>
        </div>
    </x-slot>

    @if (session('success'))
        <div class="flash-success">{{ session('success') }}</div>
    @endif

    {{-- Kartu statistik --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="card p-5">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-slate-500">Total Tiket</p>
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-brand-50 text-brand-600">
                    <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3.5 9.5V6.75A2.25 2.25 0 0 1 5.75 4.5h12.5a2.25 2.25 0 0 1 2.25 2.25V9.5a2.5 2.5 0 0 0 0 5v2.75a2.25 2.25 0 0 1-2.25 2.25H5.75a2.25 2.25 0 0 1-2.25-2.25V14.5a2.5 2.5 0 0 0 0-5Z"/></svg>
                </span>
            </div>
            <p class="mt-3 font-mono text-3xl font-semibold text-ink">{{ $totalTickets }}</p>
            <p class="mt-1 text-xs text-slate-400">Sepanjang masa</p>
        </div>
        <div class="card p-5">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-slate-500">Tiket Hari Ini</p>
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-amber-50 text-amber-600">
                    <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="12" r="8.5"/><path stroke-linecap="round" d="M12 7.5V12l3 2"/></svg>
                </span>
            </div>
            <p class="mt-3 font-mono text-3xl font-semibold text-ink">{{ $todayTickets }}</p>
            <p class="mt-1 text-xs text-slate-400">Pengajuan baru</p>
        </div>
        <div class="card p-5">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-slate-500">Status Aktif</p>
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                    <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4 17.5 10 11l3.5 3.5L20 7.5M15.5 7.5H20V12"/></svg>
                </span>
            </div>
            <p class="mt-3 font-mono text-3xl font-semibold text-ink">{{ $activeTotal }}</p>
            <p class="mt-1 text-xs text-slate-400">Sedang berjalan</p>
        </div>
    </div>
    {{-- Peta status aktif --}}
    <div class="card p-5 sm:p-6">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <h2 class="text-sm font-semibold text-ink">Tiket per Status Aktif</h2>
            <p class="text-xs text-slate-400">Klik status untuk memfilter daftar tiket</p>
        </div>
        <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-4 lg:grid-cols-7">
            @foreach ($activeStatusCounts as $item)
                <a href="{{ route('staff.tickets.index', ['status' => $item['status']->value]) }}"
                   class="group rounded-xl border border-slate-200 p-3 text-center transition hover:border-brand-300 hover:bg-brand-50/50 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500">
                    <p class="font-mono text-2xl font-semibold text-ink group-hover:text-brand-700">{{ $item['total'] }}</p>
                    <span class="mt-1.5 inline-block">
                        <x-status-badge :status="$item['status']" />
                    </span>
                </a>
            @endforeach
        </div>
    </div>

    {{-- Antrean tindak lanjut sesuai peran --}}
    <div class="card overflow-hidden">
        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4 sm:px-6">
            <h2 class="text-sm font-semibold text-ink">Tiket yang Perlu Anda Tindak Lanjuti</h2>
            <a href="{{ route('staff.tickets.index') }}" class="text-xs font-semibold text-brand-600 hover:text-brand-700">Lihat semua</a>
        </div>
        @forelse ($tickets as $ticket)
            <a href="{{ route('staff.tickets.show', $ticket) }}"
               class="flex items-center justify-between gap-4 border-b border-slate-100 px-5 py-3.5 transition last:border-0 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-brand-500 sm:px-6">
                <div class="min-w-0">
                    <p class="font-mono text-sm font-semibold text-brand-700">{{ $ticket->ticket_number }}</p>
                    <p class="mt-0.5 truncate text-xs text-slate-500">
                        {{ $ticket->customer->name }} &middot; {{ $ticket->product->name }}
                        &middot; {{ $ticket->created_at->format('d M Y H:i') }}
                    </p>
                </div>
                <x-status-badge :status="$ticket->status" class="shrink-0" />
            </a>
        @empty
            <div class="px-6 py-12 text-center">
                <p class="text-sm text-slate-400">Tidak ada tiket yang relevan untuk peran Anda saat ini.</p>
            </div>
        @endforelse
    </div>
</x-app-layout>