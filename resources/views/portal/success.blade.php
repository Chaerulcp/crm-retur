<x-guest-layout>
    <div class="flex min-h-screen flex-col bg-paper">
        @include('portal.partials.nav')

        <main class="mx-auto w-full max-w-2xl flex-1 px-4 py-14 sm:px-6">
            <div class="text-center">
                <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-emerald-100">
                    <svg class="h-7 w-7 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m5 12.5 4.5 4.5L19 7.5" />
                    </svg>
                </span>
                <h1 class="mt-5 font-display text-2xl font-bold text-ink sm:text-3xl">Retur Berhasil Diajukan</h1>
                <p class="mt-2 text-sm leading-6 text-slate-600">Terima kasih! Pengajuan retur Anda telah kami terima dan masuk antrean verifikasi.</p>
            </div>

            {{-- "Struk" nomor retur: identitas utama yang harus disimpan pelanggan --}}
            <div class="card relative mt-8 overflow-hidden p-6 sm:p-8" x-data="{ copied: false }">
                <span aria-hidden="true" class="absolute inset-x-0 top-0 h-1 bg-brand-600"></span>
                <p class="eyebrow text-center">Nomor Retur Anda</p>
                <p class="mt-3 text-center font-mono text-2xl font-semibold tracking-wider text-brand-700 sm:text-3xl">
                    {{ $ticketNumber }}
                </p>
                <div class="mt-5 flex justify-center">
                    <button type="button"
                            @click="navigator.clipboard.writeText(@js($ticketNumber)); copied = true; setTimeout(() => copied = false, 2000)"
                            class="btn-secondary px-3.5 py-2 text-xs">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <rect x="8.5" y="8.5" width="11" height="11" rx="2" />
                            <path stroke-linecap="round" d="M15.5 5.5v-1a1 1 0 0 0-1-1h-9a1 1 0 0 0-1 1v9a1 1 0 0 0 1 1h1" />
                        </svg>
                        <span x-text="copied ? 'Tersalin!' : 'Salin Nomor'"></span>
                    </button>
                </div>
                <p class="mt-5 text-center text-sm leading-6 text-slate-500">
                    Simpan nomor ini. Anda dapat memantau proses retur kapan saja melalui halaman
                    <a href="{{ route('portal.tracking') }}" class="font-medium text-brand-600 hover:text-brand-700">Lacak Retur</a>.
                </p>
            </div>

            <div class="mt-8">
                <x-status-pipeline :status="\App\Enums\TicketStatus::Diajukan" />
            </div>

            <div class="mt-8 space-y-3 text-center">
                @if ($ticketNumber && $trackingToken)
                    <a href="{{ route('portal.tracking.show', ['ticket_number' => $ticketNumber, 'token' => $trackingToken]) }}"
                       class="btn-primary w-full px-6 sm:w-auto">Lacak Retur Saya</a>
                @endif
                <p class="text-xs text-slate-400">Email konfirmasi telah dikirim ke alamat yang Anda daftarkan.</p>
            </div>
        </main>

        @include('portal.partials.footer')
    </div>
</x-guest-layout>