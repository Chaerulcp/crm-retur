<footer class="bg-white border-t border-slate-200 mt-auto">
    <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8 lg:py-16">
        <div class="xl:grid xl:grid-cols-2 xl:gap-8 items-center">
            {{-- Brand & About --}}
            <div class="space-y-6">
                <a href="/" class="flex items-center gap-2">
                    <svg class="h-6 w-6 text-brand-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                    </svg>
                    <span class="font-display text-xl font-bold tracking-tight text-slate-900">Retunly</span>
                </a>
                <p class="text-sm leading-6 text-slate-600 max-w-sm">
                    Platform SaaS terdepan untuk mengotomatisasi proses retur dan refund e-commerce dengan kekuatan AI Vision.
                </p>
            </div>

            {{-- Links --}}
            <div class="mt-10 xl:mt-0 flex gap-12 xl:justify-end">
                <div>
                    <h3 class="text-sm font-semibold leading-6 text-slate-900">Layanan</h3>
                    <ul role="list" class="mt-4 space-y-3">
                        <li><a href="{{ route('portal.create') }}" class="text-sm leading-6 text-slate-600 hover:text-slate-900 transition-colors">Ajukan Retur</a></li>
                        <li><a href="{{ route('portal.tracking') }}" class="text-sm leading-6 text-slate-600 hover:text-slate-900 transition-colors">Lacak Retur</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-sm font-semibold leading-6 text-slate-900">Bantuan</h3>
                    <ul role="list" class="mt-4 space-y-3">
                        <li><a href="{{ route('portal.faq') }}" class="text-sm leading-6 text-slate-600 hover:text-slate-900 transition-colors">Pusat Bantuan (FAQ)</a></li>
                        <li><a href="mailto:support@retunly.tech" class="text-sm leading-6 text-slate-600 hover:text-slate-900 transition-colors">Hubungi CS</a></li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="mt-12 border-t border-slate-200 pt-8">
            <p class="text-xs leading-5 text-slate-500">
                &copy; {{ date('Y') }} Retunly. Seluruh hak cipta dilindungi.
            </p>
        </div>
    </div>
</footer>