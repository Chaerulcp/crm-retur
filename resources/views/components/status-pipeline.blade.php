@props(['status'])

{{--
    "Jalur Retur" — visualisasi perjalanan tiket dari Diajukan sampai Selesai.
    Elemen khas (signature) aplikasi: 8 tahap utama pada satu garis; tahap
    berjalan ditandai titik gelap dengan cincin, tahap terlewati teal penuh.
    Status Ditolak berada di luar jalur utama dan digambar sebagai titik merah.
--}}

@php
    use App\Enums\TicketStatus;

    $pipelineStages = [
        TicketStatus::Diajukan,
        TicketStatus::Diverifikasi,
        TicketStatus::Disetujui,
        TicketStatus::MenungguBarang,
        TicketStatus::BarangDiterima,
        TicketStatus::PemeriksaanGudang,
        TicketStatus::RefundDiproses,
        TicketStatus::Selesai,
    ];

    $pipelineRejected = $status === TicketStatus::Ditolak;
    $pipelineIndex = $pipelineRejected ? 1 : (int) array_search($status, $pipelineStages, true);
    $pipelineProgress = $pipelineIndex / (count($pipelineStages) - 1);
@endphp

<div {{ $attributes->merge(['class' => 'overflow-x-auto']) }}>
    <div class="relative min-w-[540px] py-1">
        {{-- Garis dasar & garis progres (membentang antar titik tengah node pertama-terakhir) --}}
        <span aria-hidden="true" class="absolute left-[6.25%] right-[6.25%] top-[13px] h-0.5 rounded-full bg-slate-200"></span>
        <span aria-hidden="true"
              class="absolute left-[6.25%] top-[13px] h-0.5 rounded-full transition-all duration-500 {{ $pipelineRejected ? 'bg-red-300' : 'bg-brand-600' }}"
              style="width: {{ number_format($pipelineProgress * 87.5, 2) }}%"></span>

        <ol class="relative grid grid-cols-8">
            @foreach ($pipelineStages as $i => $stage)
                @php
                    if ($pipelineRejected) {
                        $nodeState = $i < 1 ? 'done' : ($i === 1 ? 'rejected' : 'todo');
                    } else {
                        $nodeState = $i < $pipelineIndex ? 'done' : ($i === $pipelineIndex ? 'current' : 'todo');
                    }
                @endphp
                <li class="flex flex-col items-center gap-1.5 text-center">
                    <span class="flex h-7 w-7 items-center justify-center rounded-full border-2 font-mono text-[11px] font-semibold
                        @if ($nodeState === 'done') border-brand-600 bg-brand-600 text-white
                        @elseif ($nodeState === 'current') border-ink bg-ink text-white ring-4 ring-brand-100
                        @elseif ($nodeState === 'rejected') border-red-600 bg-red-600 text-white ring-4 ring-red-100
                        @else border-slate-300 bg-white text-slate-400
                        @endif">
                        @if ($nodeState === 'done')
                            <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M16.704 5.29a1 1 0 0 1 .006 1.414l-7.5 7.584a1 1 0 0 1-1.427-.006L3.29 9.706a1 1 0 1 1 1.42-1.408l3.784 3.815 6.796-6.87a1 1 0 0 1 1.414-.006Z" clip-rule="evenodd" />
                            </svg>
                        @elseif ($nodeState === 'rejected')
                            <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z" />
                            </svg>
                        @else
                            {{ $i + 1 }}
                        @endif
                    </span>
                    <span class="px-0.5 text-[11px] leading-tight
                        @if ($nodeState === 'current') font-semibold text-ink
                        @elseif ($nodeState === 'rejected') font-semibold text-red-700
                        @elseif ($nodeState === 'done') font-medium text-brand-700
                        @else text-slate-400
                        @endif">
                        {{ $pipelineRejected && $i === 1 ? 'Ditolak' : $stage->label() }}
                    </span>
                </li>
            @endforeach
        </ol>
    </div>
</div>