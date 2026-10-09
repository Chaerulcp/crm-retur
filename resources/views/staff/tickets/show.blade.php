@php
    $isWarehouseVerdict = $ticket->status === $warehouseVerdictStatus && count($availableTransitions) > 0;
    $isRefundProcessing = $ticket->status === $refundProcessingStatus;
@endphp
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="eyebrow">Tiket Retur</p>
                <h1 class="mt-1 font-mono text-xl font-semibold tracking-wide text-ink sm:text-2xl">{{ $ticket->ticket_number }}</h1>
                <p class="mt-1 text-sm text-slate-500">
                    Diajukan {{ $ticket->created_at->format('d M Y H:i') }}
                    @if ($ticket->assignee) &middot; Petugas terakhir: {{ $ticket->assignee->name }} @endif
                </p>
            </div>
            <x-status-badge :status="$ticket->status" />
        </div>
    </x-slot>

    @if (session('success'))
        <div class="flash-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="flash-error">{{ session('error') }}</div>
    @endif
    @if ($errors->any())
        <div class="flash-error">
            <ul class="list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Jalur Retur: posisi tiket pada alur kerja --}}
    <div class="card px-4 py-6 sm:px-7">
        <x-status-pipeline :status="$ticket->status" />
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        {{-- Kolom kiri: detail, bukti, riwayat, komunikasi --}}
        <div class="space-y-6 lg:col-span-2">

            {{-- Detail tiket --}}
            <div class="card p-5 sm:p-6">
                <h2 class="eyebrow">Detail Tiket</h2>
                <dl class="mt-4 grid grid-cols-1 gap-5 text-sm sm:grid-cols-2">
                    <div>
                        <dt class="text-slate-500">Pelanggan</dt>
                        <dd class="mt-1 font-semibold text-ink">
                            {{ $ticket->customer->name }}
                            <span class="mt-0.5 block text-xs font-normal text-slate-500">
                                {{ $ticket->customer->email }}
                                @if ($ticket->customer->phone) &middot; {{ $ticket->customer->phone }} @endif
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Produk</dt>
                        <dd class="mt-1 font-semibold text-ink">
                            {{ $ticket->product->name }}
                            @if ($ticket->product->sku)
                                <span class="mt-0.5 block font-mono text-xs font-normal text-slate-500">SKU: {{ $ticket->product->sku }}</span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Nomor Faktur</dt>
                        <dd class="mt-1 font-mono text-sm font-medium text-ink">{{ $ticket->invoice_number ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Kondisi Barang</dt>
                        <dd class="mt-1 font-semibold text-ink">{{ $ticket->item_condition?->label() ?? '-' }}</dd>
                    </div>
                    @if ($ticket->refund_method)
                        <div>
                            <dt class="text-slate-500">Metode Refund</dt>
                            <dd class="mt-1 font-semibold text-ink">
                                {{ $ticket->refund_method }}
                                @if ($ticket->refund_proof_path)
                                    <a href="{{ \Illuminate\Support\Facades\Storage::url($ticket->refund_proof_path) }}"
                                       target="_blank" rel="noopener" class="mt-0.5 block text-xs font-medium text-brand-600 hover:underline">
                                        Lihat bukti refund
                                    </a>
                                @endif
                            </dd>
                        </div>
                    @endif
                    <div class="sm:col-span-2">
                        <dt class="text-slate-500">Alasan Retur</dt>
                        <dd class="mt-1 leading-6 text-slate-700">{{ $ticket->reason }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Bukti retur --}}
            <div class="card p-5 sm:p-6">
                <h2 class="eyebrow">Bukti Retur</h2>

                @if ($ticket->ai_analysis_result)
                    <div class="mt-4 rounded-xl border border-ai-DEFAULT/20 bg-ai-DEFAULT/5 p-4 relative overflow-hidden">
                        <div class="absolute -right-4 -top-4 w-16 h-16 bg-ai-DEFAULT/20 rounded-full blur-xl"></div>
                        <div class="flex items-start gap-3 relative z-10">
                            <div class="flex-shrink-0 mt-0.5">
                                <span class="flex h-5 w-5 items-center justify-center rounded-full bg-ai-DEFAULT/20 text-ai-DEFAULT">
                                    <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l2.4 7.4L22 12l-7.6 2.6L12 22l-2.4-7.4L2 12l7.6-2.6L12 2z"/></svg>
                                </span>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-slate-800">Analisis Claude Vision</h3>
                                <p class="mt-1 text-sm text-slate-600 leading-relaxed">{{ $ticket->ai_analysis_result }}</p>
                                @if ($ticket->ai_fraud_score !== null)
                                    <div class="mt-3 flex items-center gap-2 text-xs font-semibold">
                                        <span class="text-slate-500">Fraud Score:</span>
                                        <span class="rounded-full px-2 py-0.5 {{ $ticket->ai_fraud_score > 50 ? 'bg-red-100 text-red-700' : 'bg-emerald-100 text-emerald-700' }}">
                                            {{ $ticket->ai_fraud_score }}/100
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                @forelse ($evidences as $evidence)
                    <figure class="mt-4 inline-block align-top mr-4">
                        @if ($evidence->isVideo())
                            <video src="{{ $evidence->url() }}" controls class="h-44 rounded-lg border border-slate-200 bg-slate-900 object-contain"></video>
                        @else
                            <a href="{{ $evidence->url() }}" target="_blank" rel="noopener"
                               class="block overflow-hidden rounded-lg border border-slate-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500">
                                <img src="{{ $evidence->url() }}" alt="{{ $evidence->original_name }}" class="h-44 object-cover transition hover:scale-[1.02]">
                            </a>
                        @endif
                        <figcaption class="mt-1.5 max-w-[11rem] truncate font-mono text-xs text-slate-500">{{ $evidence->original_name }}</figcaption>
                    </figure>
                @empty
                    <p class="mt-3 text-sm text-slate-400">Belum ada bukti yang diunggah.</p>
                @endforelse
            </div>
            {{-- Timeline riwayat status --}}
            <div class="card p-5 sm:p-6">
                <h2 class="eyebrow">Riwayat Status</h2>
                <ol class="mt-5 space-y-5 border-l-2 border-slate-200 pl-5">
                    <li class="relative">
                        <span aria-hidden="true" class="absolute -left-[27px] top-1 h-3 w-3 rounded-full bg-slate-300 ring-4 ring-slate-100"></span>
                        <p class="text-sm font-medium text-slate-700">Tiket dibuat oleh pelanggan</p>
                        <p class="mt-0.5 font-mono text-xs text-slate-400">{{ $ticket->created_at->format('d M Y H:i') }}</p>
                    </li>
                    @foreach ($ticket->statusHistories as $history)
                        <li class="relative">
                            <span aria-hidden="true" class="absolute -left-[27px] top-1 h-3 w-3 rounded-full bg-brand-500 ring-4 ring-brand-100"></span>
                            <p class="text-sm text-slate-700">
                                @if ($history->from_status)
                                    {{ \App\Enums\TicketStatus::from($history->from_status)->label() }} &rarr;
                                @endif
                                <span class="font-semibold text-ink">{{ \App\Enums\TicketStatus::from($history->to_status)->label() }}</span>
                                @if ($history->user)
                                    <span class="text-slate-500">oleh {{ $history->user->name }}</span>
                                @endif
                            </p>
                            @if ($history->note)
                                <p class="mt-1 text-sm leading-6 text-slate-600">{{ $history->note }}</p>
                            @endif
                            <p class="mt-0.5 font-mono text-xs text-slate-400">{{ $history->created_at->format('d M Y H:i') }}</p>
                        </li>
                    @endforeach
                </ol>
            </div>

            {{-- Daftar komunikasi --}}
            <div class="card p-5 sm:p-6">
                <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
                    <h2 class="eyebrow">Komunikasi &amp; Catatan</h2>
                    <form method="POST" action="{{ route('staff.tickets.copilot', $ticket) }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-ai-DEFAULT/10 px-3 py-1.5 text-xs font-semibold text-ai-DEFAULT transition hover:bg-ai-DEFAULT/20" title="Analisis sentimen dan buat draf balasan otomatis">
                            ✨ AI Copilot
                        </button>
                    </form>
                </div>

                @if ($ticket->ai_summary)
                    <div class="mb-5 rounded-xl border border-ai-DEFAULT/20 bg-gradient-to-r from-ai-DEFAULT/5 to-transparent p-4">
                        <div class="flex items-center gap-2 text-xs font-bold text-ai-DEFAULT uppercase tracking-wider mb-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            Ringkasan AI
                            @if($ticket->ai_sentiment)
                                <span class="ml-auto rounded-full bg-white px-2 py-0.5 shadow-sm border border-slate-100 text-slate-700">
                                    Sentimen: {{ $ticket->ai_sentiment }}
                                </span>
                            @endif
                        </div>
                        <p class="text-sm text-slate-700 leading-relaxed">{{ $ticket->ai_summary }}</p>
                    </div>
                @endif
                @forelse ($ticket->communications as $communication)
                    <div class="mt-4 rounded-lg border border-slate-200 bg-slate-50/60 p-4">
                        <div class="flex flex-wrap items-center justify-between gap-2 text-xs text-slate-500">
                            <span>
                                {{ $communication->sender?->name ?? 'Sistem' }}
                                ({{ $communication->sender_type->label() }})
                                &middot; {{ $communication->created_at->format('d M Y H:i') }}
                            </span>
                            @if ($communication->is_internal)
                                <span class="rounded-full bg-amber-100 px-2 py-0.5 font-semibold text-amber-700">Internal</span>
                            @endif
                        </div>
                        <p class="mt-2 text-sm leading-6 text-slate-700">{{ $communication->message }}</p>
                    </div>
                @empty
                    <p class="mt-3 text-sm text-slate-400">Belum ada komunikasi.</p>
                @endforelse
            </div>
        </div>
        {{-- Kolom kanan: aksi transisi, komunikasi, live chat --}}
        <div class="space-y-6">

            {{-- Aksi status --}}
            <div class="card p-5 sm:p-6">
                <h2 class="eyebrow">Aksi Status</h2>

                {{-- Tombol transisi generik dari availableTransitions() --}}
                @foreach ($availableTransitions as $target)
                    @continue($isWarehouseVerdict) {{-- Vonis gudang lewat form kondisi --}}
                    @continue($isRefundProcessing && $target->value === 'Selesai') {{-- Lewat form refund --}}
                    <form method="POST" action="{{ route('staff.tickets.transition', $ticket) }}"
                          class="mt-4 rounded-xl border border-slate-200 p-4">
                        @csrf
                        <input type="hidden" name="status" value="{{ $target->value }}">
                        <label class="label text-xs">Catatan (opsional)</label>
                        <input type="text" name="note" maxlength="1000" placeholder="Tambahkan catatan..."
                               class="input mt-1.5">
                        <button type="submit" class="mt-3 w-full {{ $target === \App\Enums\TicketStatus::Ditolak ? 'btn-danger' : 'btn-primary' }}">
                            Ubah ke {{ $target->label() }}
                        </button>
                    </form>
                @endforeach

                {{-- Vonis gudang: Layak / Tidak Layak --}}
                @if ($isWarehouseVerdict)
                    <form method="POST" action="{{ route('staff.tickets.condition', $ticket) }}"
                          class="mt-4 space-y-3 rounded-xl border border-slate-200 p-4">
                        @csrf
                        <p class="label text-xs">Vonis kondisi barang</p>
                        <label class="flex items-center gap-2.5 text-sm text-slate-700">
                            <input type="radio" name="condition" value="Layak" required
                                   class="border-slate-300 text-brand-600 focus:ring-brand-500">
                            Layak &rarr; Refund Diproses
                        </label>
                        <label class="flex items-center gap-2.5 text-sm text-slate-700">
                            <input type="radio" name="condition" value="Tidak Layak"
                                   class="border-slate-300 text-brand-600 focus:ring-brand-500">
                            Tidak Layak &rarr; Selesai (retur ditolak)
                        </label>
                        <input type="text" name="note" maxlength="1000" placeholder="Catatan vonis (opsional)"
                               class="input">
                        <button type="submit" class="w-full rounded-lg bg-violet-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-violet-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-violet-600">
                            Simpan Vonis
                        </button>
                    </form>
                @endif
                {{-- Penyelesaian refund (Manajemen/Admin) --}}
                @if ($isRefundProcessing && auth()->user()->hasRole(\App\Enums\Role::Manajemen, \App\Enums\Role::Admin))
                    <form method="POST" action="{{ route('staff.tickets.refund', $ticket) }}" enctype="multipart/form-data"
                          class="mt-4 space-y-3 rounded-xl border border-slate-200 p-4">
                        @csrf
                        <p class="label text-xs">Proses Refund</p>
                        <div>
                            <label class="label text-xs" for="refund_method">Metode refund</label>
                            <select name="refund_method" id="refund_method" required class="input mt-1.5">
                                <option value="">Pilih metode...</option>
                                @foreach ($refundMethods as $method)
                                    <option value="{{ $method }}" @selected(old('refund_method') === $method)>{{ $method }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="label text-xs" for="refund_proof">Bukti refund (wajib)</label>
                            <input type="file" name="refund_proof" id="refund_proof" required accept=".jpg,.jpeg,.png,.webp,.pdf"
                                   class="mt-1.5 block w-full text-sm text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-3 file:py-2 file:text-xs file:font-semibold file:text-brand-700 hover:file:bg-brand-100">
                        </div>
                        <input type="text" name="note" maxlength="1000" placeholder="Catatan refund (opsional)"
                               class="input">
                        <button type="submit" class="w-full rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-600">
                            Selesaikan Refund
                        </button>
                    </form>
                @endif

                @if ($ticket->status->isTerminal())
                    <p class="mt-4 rounded-lg bg-slate-50 px-3.5 py-3 text-xs text-slate-500">Tiket sudah berada di status akhir.</p>
                @elseif ($isRefundProcessing && ! auth()->user()->hasRole(\App\Enums\Role::Manajemen, \App\Enums\Role::Admin))
                    <p class="mt-4 rounded-lg bg-slate-50 px-3.5 py-3 text-xs text-slate-500">Tahap ini menunggu penyelesaian refund oleh Manajemen/Admin.</p>
                @elseif (count($availableTransitions) === 0)
                    <p class="mt-4 rounded-lg bg-slate-50 px-3.5 py-3 text-xs text-slate-500">Tidak ada aksi status untuk peran Anda pada tahap ini.</p>
                @endif
            </div>

            {{-- Tambah komunikasi --}}
            <div class="card p-5 sm:p-6">
                <h2 class="eyebrow">Tambah Komunikasi</h2>
                @if(session('draft_reply'))
                    <div class="mt-3 mb-2 rounded-lg bg-brand-50 p-3 text-xs text-brand-700 flex gap-2 items-start border border-brand-100">
                        <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Draf balasan AI berhasil dimuat. Silakan periksa dan sesuaikan sebelum dikirim.
                    </div>
                @endif
                <form method="POST" action="{{ route('staff.tickets.communicate', $ticket) }}" class="mt-3 space-y-3">
                    @csrf
                    <textarea name="message" rows="4" required placeholder="Tulis pesan atau catatan..."
                              class="input">{{ old('message', session('draft_reply')) }}</textarea>
                    <label class="flex items-center gap-2.5 text-sm text-slate-600">
                        <input type="checkbox" name="is_internal" value="1"
                               class="rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                        Catatan internal (tidak ditampilkan ke pelanggan)
                    </label>
                    <button type="submit" class="btn-primary w-full">Kirim</button>
                </form>
            </div>

            {{-- Toggle live chat + widget --}}
            <div class="card overflow-hidden">
                <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                    <h2 class="text-sm font-semibold text-ink">Live Chat</h2>
                    <form method="POST" action="{{ route('staff.tickets.chat-toggle', $ticket) }}">
                        @csrf
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 text-xs font-semibold transition {{ $ticket->chat_active ? 'bg-red-50 text-red-700 ring-1 ring-inset ring-red-200 hover:bg-red-100' : 'bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-200 hover:bg-emerald-100' }}">
                            <span class="h-1.5 w-1.5 rounded-full {{ $ticket->chat_active ? 'bg-red-500' : 'bg-emerald-500' }}"></span>
                            {{ $ticket->chat_active ? 'Nonaktifkan Chat' : 'Aktifkan Chat' }}
                        </button>
                    </form>
                </div>
                <div class="p-5">
                    @include('chat.widget', ['ticket' => $ticket, 'chatContext' => 'staff'])
                </div>
            </div>

        </div>
    </div>
</x-app-layout>