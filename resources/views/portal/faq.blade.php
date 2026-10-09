<x-guest-layout>
    @section('seo_title', 'Pusat Bantuan & FAQ — Retunly')
    @section('seo_description', 'Temukan jawaban atas pertanyaan umum seputar proses pengembalian barang, refund, dan cara menggunakan platform Retunly.')
    @section('seo_canonical', 'https://retunly.tech/faq')

    @push('json_ld')
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "FAQPage",
        "mainEntity": [
            @isset($faqs)
                @foreach($faqs->flatten() as $faq)
                {
                    "@type": "Question",
                    "name": "{{ addslashes($faq->question) }}",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "{{ addslashes($faq->answer) }}"
                    }
                }@if(!$loop->last),@endif
                @endforeach
            @endisset
        ]
    }
    </script>
    @endpush

    <div class="flex min-h-screen flex-col bg-slate-50 font-sans text-slate-900 selection:bg-brand-100 selection:text-brand-900">
        @include('portal.partials.nav')

        <main class="mx-auto w-full max-w-3xl flex-1 px-4 py-16 sm:px-6">
            <div class="text-center mb-12">
                <h1 class="font-display text-4xl font-bold tracking-tight text-slate-900 sm:text-5xl">Pusat Bantuan</h1>
                <p class="mt-4 text-lg text-slate-600">
                    Temukan jawaban atas pertanyaan yang paling sering diajukan seputar proses pengembalian barang kami.
                </p>
            </div>

            <div class="space-y-12">
                @forelse ($faqs as $category => $items)
                    <div>
                        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-900 mb-4">{{ $category }}</h2>
                        <div class="overflow-hidden rounded-xl bg-white shadow-sm border border-slate-200 divide-y divide-slate-100">
                            @foreach ($items as $faq)
                                <details class="group">
                                    <summary class="flex cursor-pointer items-center justify-between gap-4 px-6 py-5 text-base font-semibold text-slate-900 transition hover:bg-slate-50 focus:outline-none focus-visible:bg-slate-50">
                                        {{ $faq->question }}
                                        <svg class="h-5 w-5 shrink-0 text-slate-400 transition-transform group-open:rotate-180" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                        </svg>
                                    </summary>
                                    <div class="px-6 pb-6 pt-2 text-sm leading-relaxed text-slate-600">
                                        {{ $faq->answer }}
                                    </div>
                                </details>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="rounded-xl bg-white shadow-sm border border-slate-200 p-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-slate-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <h3 class="text-lg font-bold text-slate-900 mb-2">FAQ Belum Tersedia</h3>
                        <p class="text-sm text-slate-500 max-w-md mx-auto mb-6">Pusat Bantuan saat ini sedang dalam pembaruan. Silakan ajukan retur langsung jika Anda mengalami kendala.</p>
                        <a href="{{ route('portal.create') }}" class="inline-flex items-center justify-center rounded-lg bg-slate-900 px-6 py-2.5 text-sm font-medium text-white transition-colors hover:bg-slate-800">Ajukan Retur</a>
                    </div>
                @endforelse
            </div>
            
            <div class="mt-16 text-center">
                <p class="text-sm text-slate-500">Masih punya pertanyaan?</p>
                <a href="mailto:support@retunly.tech" class="mt-2 inline-flex font-medium text-slate-900 hover:underline">Hubungi Tim Dukungan Kami &rarr;</a>
            </div>
        </main>

        @include('portal.partials.footer')
    </div>
</x-guest-layout>