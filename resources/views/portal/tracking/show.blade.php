<x-guest-layout>
    <div class="flex min-h-screen flex-col bg-paper">
        @include('portal.partials.nav')

        <main class="mx-auto w-full max-w-3xl flex-1 px-4 py-10 sm:px-6">
            {{-- Kepala tiket + Jalur Retur --}}
            <div class="card overflow-hidden">
                <div class="flex flex-wrap items-start justify-between gap-4 border-b border-slate-100 p-6 sm:p-7">
                    <div>
                        <p class="eyebrow">Nomor Retur</p>
                        <p class="mt-1.5 font-mono text-xl font-semibold tracking-wide text-ink sm:text-2xl">{{ $ticket->ticket_number }}</p>
                        <p class="mt-1 text-xs text-slate-400">Diajukan {{ $ticket->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <x-status-badge :status="$ticket->status" />
                </div>
                <div class="bg-slate-50/70 px-4 py-6 sm:px-7">
                    <x-status-pipeline :status="$ticket->status" />
                </div>
            </div>

            {{-- Rincian tiket --}}
            <section class="card mt-6 p-6 sm:p-7">
                <h2 class="eyebrow">Rincian Pengajuan</h2>
                <dl class="mt-4 grid gap-x-8 gap-y-4 text-sm sm:grid-cols-2">
                    <div class="flex items-baseline justify-between gap-4 sm:block">
                        <dt class="text-slate-500">Produk</dt>
                        <dd class="mt-0.5 font-semibold text-ink">{{ $ticket->product->name }}</dd>
                    </div>
                    @if ($ticket->invoice_number)
                        <div class="flex items-baseline justify-between gap-4 sm:block">
                            <dt class="text-slate-500">Nomor Invoice</dt>
                            <dd class="mt-0.5 font-mono text-sm font-medium text-ink">{{ $ticket->invoice_number }}</dd>
                        </div>
                    @endif
                    @if ($ticket->refund_method)
                        <div class="flex items-baseline justify-between gap-4 sm:block">
                            <dt class="text-slate-500">Metode Refund</dt>
                            <dd class="mt-0.5 font-semibold text-ink">{{ $ticket->refund_method }}</dd>
                        </div>
                    @endif
                    <div class="flex items-baseline justify-between gap-4 sm:block">
                        <dt class="text-slate-500">Tanggal Pengajuan</dt>
                        <dd class="mt-0.5 font-semibold text-ink">{{ $ticket->created_at->format('d/m/Y H:i') }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-slate-500">Alasan Retur</dt>
                        <dd class="mt-1 leading-6 text-slate-700">{{ $ticket->reason }}</dd>
                    </div>
                </dl>
            </section>

            {{-- Bukti pendukung --}}
            <section class="card mt-6 p-6 sm:p-7">
                <h2 class="eyebrow">Bukti Pendukung</h2>
                @if ($ticket->evidences->count())
                    <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3">
                        @foreach ($ticket->evidences as $evidence)
                            @if ($evidence->isImage())
                                <a href="{{ $evidence->url() }}" target="_blank" rel="noopener"
                                   class="overflow-hidden rounded-lg border border-slate-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500">
                                    <img src="{{ $evidence->url() }}" alt="Bukti {{ $evidence->original_name }}"
                                         class="h-32 w-full object-cover transition hover:scale-[1.02]">
                                </a>
                            @else
                                <video src="{{ $evidence->url() }}" controls
                                       class="h-32 w-full rounded-lg border border-slate-200 bg-slate-900 object-contain"></video>
                            @endif
                        @endforeach
                    </div>
                @else
                    <p class="mt-3 text-sm text-slate-400">Belum ada bukti yang dilampirkan.</p>
                @endif
            </section>
            {{-- Riwayat komunikasi (catatan internal staf disembunyikan) --}}
            @php
                $publicUpdates = $ticket->communications
                    ->where('is_internal', false)
                    ->sortBy('created_at')
                    ->values();
            @endphp

            <section class="card mt-6 p-6 sm:p-7">
                <h2 class="eyebrow">Riwayat Komunikasi</h2>
                @if ($publicUpdates->count())
                    <ul class="mt-4 space-y-3">
                        @foreach ($publicUpdates as $update)
                            <li class="rounded-lg border border-slate-200 bg-slate-50/60 p-4">
                                <p class="text-sm leading-6 text-slate-800">{{ $update->message }}</p>
                                <p class="mt-1.5 text-xs text-slate-400">
                                    {{ $update->sender_type->label() }} &middot; {{ $update->created_at->format('d/m/Y H:i') }}
                                </p>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="mt-3 text-sm text-slate-400">Belum ada komunikasi.</p>
                @endif
            </section>

            {{-- Timeline riwayat status --}}
            <section class="card mt-6 p-6 sm:p-7">
                <h2 class="eyebrow">Riwayat Status</h2>
                @if ($ticket->statusHistories->count())
                    <ol class="mt-5 space-y-5 border-l-2 border-slate-200 pl-5">
                        @foreach ($ticket->statusHistories->sortBy('created_at') as $history)
                            <li class="relative">
                                <span aria-hidden="true" class="absolute -left-[27px] top-1 flex h-3 w-3 items-center justify-center">
                                    <span class="h-3 w-3 rounded-full bg-brand-600 ring-4 ring-brand-100"></span>
                                </span>
                                <p class="text-sm font-semibold text-ink">
                                    {{ \App\Enums\TicketStatus::from($history->to_status)->label() }}
                                </p>
                                @if ($history->note)
                                    <p class="mt-1 text-sm leading-6 text-slate-600">{{ $history->note }}</p>
                                @endif
                                <p class="mt-1 font-mono text-xs text-slate-400">{{ $history->created_at->format('d/m/Y H:i') }}</p>
                            </li>
                        @endforeach
                    </ol>
                @else
                    <p class="mt-3 text-sm text-slate-400">Belum ada riwayat status.</p>
                @endif
            </section>

            {{-- Live chat (token dilampirkan pada atribut data widget) --}}
            <section class="mt-6" data-chat-token="{{ $ticket->tracking_token }}">
                @include('chat.widget', ['ticket' => $ticket, 'chatContext' => 'portal'])
            </section>
        </main>

        @include('portal.partials.footer')
    </div>
</x-guest-layout>