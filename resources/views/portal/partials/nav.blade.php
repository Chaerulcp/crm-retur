@php
    $portalLinks = [
        ['href' => route('portal.home'), 'label' => __('Beranda'), 'active' => request()->routeIs('portal.home')],
        ['href' => route('portal.create'), 'label' => __('Ajukan Retur'), 'active' => request()->routeIs('portal.create', 'portal.success')],
        ['href' => route('portal.tracking'), 'label' => __('Lacak Retur'), 'active' => request()->routeIs('portal.tracking*')],
        ['href' => route('portal.faq'), 'label' => __('FAQ'), 'active' => request()->routeIs('portal.faq')],
    ];
@endphp

<header class="sticky top-0 z-50 w-full border-b border-slate-200 bg-white/90 backdrop-blur-md">
    <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
        <a href="/" class="flex items-center gap-2">
            <svg class="h-6 w-6 text-brand-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                <polyline points="9 22 9 12 15 12 15 22"></polyline>
            </svg>
            <span class="font-display text-xl font-bold tracking-tight text-slate-900">Retunly</span>
            <span class="ml-2 hidden sm:inline-flex items-center rounded-md bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-600 ring-1 ring-inset ring-slate-200">Portal</span>
        </a>

        <nav class="hidden md:flex items-center gap-8">
            @foreach ($portalLinks as $link)
                <a href="{{ $link['href'] }}" class="text-sm font-medium transition-colors {{ $link['active'] ? 'text-slate-900' : 'text-slate-500 hover:text-slate-900' }}">
                    {{ $link['label'] }}
                </a>
            @endforeach
        </nav>

        <div class="flex items-center gap-4">
            <div class="flex items-center gap-2 text-sm font-medium">
                <a href="{{ route('lang.switch', 'id') }}" class="transition-colors {{ session('locale', config('app.locale')) === 'id' ? 'text-slate-900 font-bold' : 'text-slate-400 hover:text-slate-600' }}">ID</a>
                <span class="text-slate-300">|</span>
                <a href="{{ route('lang.switch', 'en') }}" class="transition-colors {{ session('locale', config('app.locale')) === 'en' ? 'text-slate-900 font-bold' : 'text-slate-400 hover:text-slate-600' }}">EN</a>
            </div>
            <a href="{{ route('login') }}" class="text-sm font-medium text-slate-600 hover:text-slate-900 hidden sm:block transition-colors">{{ __('Log in') }}</a>
            <a href="{{ route('portal.create') }}" class="inline-flex items-center justify-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-900 focus:ring-offset-2">
                {{ __('Ajukan Retur') }}
            </a>
        </div>
    </div>
    
    {{-- Mobile nav --}}
    <nav class="flex gap-4 overflow-x-auto border-t border-slate-100 bg-white px-4 py-3 md:hidden">
        @foreach ($portalLinks as $link)
            <a href="{{ $link['href'] }}" class="whitespace-nowrap text-sm font-medium transition-colors {{ $link['active'] ? 'text-slate-900' : 'text-slate-500 hover:text-slate-900' }}">
                {{ $link['label'] }}
            </a>
        @endforeach
    </nav>
</header>