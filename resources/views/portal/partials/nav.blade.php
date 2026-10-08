@php
    $portalLinks = [
        ['href' => route('portal.home'), 'label' => 'Beranda', 'active' => request()->routeIs('portal.home')],
        ['href' => route('portal.create'), 'label' => 'Ajukan Retur', 'active' => request()->routeIs('portal.create', 'portal.success')],
        ['href' => route('portal.tracking'), 'label' => 'Lacak Retur', 'active' => request()->routeIs('portal.tracking*')],
        ['href' => route('portal.faq'), 'label' => 'FAQ', 'active' => request()->routeIs('portal.faq')],
    ];
@endphp

<header class="sticky top-0 z-20 border-b border-slate-200/80 bg-white/90 backdrop-blur">
    <div class="mx-auto flex h-16 max-w-5xl items-center justify-between gap-4 px-4 sm:px-6">
        <a href="{{ route('portal.home') }}" class="flex items-center gap-2.5 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 rounded-lg">
            <x-application-logo class="h-8 w-8" />
            <span class="font-display text-lg font-bold tracking-tight text-ink">
                CRM <span class="text-brand-600">Retur</span>
            </span>
        </a>

        <nav class="hidden items-center gap-1 md:flex" aria-label="Navigasi portal">
            @foreach ($portalLinks as $link)
                <a href="{{ $link['href'] }}"
                   class="rounded-full px-3.5 py-2 text-sm font-medium transition focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 {{ $link['active'] ? 'bg-brand-50 text-brand-700' : 'text-slate-600 hover:bg-slate-50 hover:text-ink' }}">
                    {{ $link['label'] }}
                </a>
            @endforeach
        </nav>

        <a href="{{ route('portal.create') }}" class="btn-primary hidden px-3.5 py-2 text-xs md:inline-flex">
            Ajukan Retur
        </a>
    </div>

    {{-- Navigasi layar kecil --}}
    <nav class="flex gap-1 overflow-x-auto px-4 pb-3 md:hidden" aria-label="Navigasi portal">
        @foreach ($portalLinks as $link)
            <a href="{{ $link['href'] }}"
               class="whitespace-nowrap rounded-full px-3.5 py-1.5 text-sm font-medium {{ $link['active'] ? 'bg-brand-50 text-brand-700' : 'text-slate-600 hover:bg-slate-50 hover:text-ink' }}">
                {{ $link['label'] }}
            </a>
        @endforeach
    </nav>
</header>