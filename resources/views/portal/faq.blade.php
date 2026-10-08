<x-guest-layout>
    <div class="flex min-h-screen flex-col bg-paper">
        @include('portal.partials.nav')

        <main class="mx-auto w-full max-w-3xl flex-1 px-4 py-10 sm:px-6">
            <p class="eyebrow">Pusat Bantuan</p>
            <h1 class="mt-2 font-display text-2xl font-bold text-ink sm:text-3xl">Pertanyaan Umum</h1>
            <p class="mt-2 text-sm leading-6 text-slate-600">Jawaban untuk pertanyaan yang sering diajukan seputar proses retur.</p>

            @forelse ($faqs as $category => $items)
                <h2 class="mt-10 text-xs font-semibold uppercase tracking-wider text-brand-600">{{ $category }}</h2>
                <div class="card mt-3 divide-y divide-slate-100 overflow-hidden">
                    @foreach ($items as $faq)
                        <details class="group">
                            <summary class="flex cursor-pointer items-center justify-between gap-4 px-5 py-4 text-sm font-semibold text-ink transition hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-brand-500">
                                {{ $faq->question }}
                                <svg class="h-4 w-4 shrink-0 text-slate-400 transition-transform group-open:rotate-180" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                </svg>
                            </summary>
                            <p class="px-5 pb-5 text-sm leading-6 text-slate-600">{{ $faq->answer }}</p>
                        </details>
                    @endforeach
                </div>
            @empty
                <div class="card mt-8 p-10 text-center">
                    <p class="text-sm text-slate-500">Belum ada FAQ yang tersedia.</p>
                    <a href="{{ route('portal.create') }}" class="btn-primary mt-4">Ajukan Retur</a>
                </div>
            @endforelse
        </main>

        @include('portal.partials.footer')
    </div>
</x-guest-layout>