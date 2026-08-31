{{--
    Sidebar "Panel Operasional" — navigasi utama staf/admin.
    Drawer (x-data navOpen dari layouts/app) pada layar kecil, tetap pada lg+.
--}}

@php
    use App\Enums\Role;

    $navItem = 'group flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm font-medium transition focus:outline-none focus-visible:ring-2 focus-visible:ring-white/60';
    $navActive = 'bg-white/10 text-white';
    $navIdle = 'text-brand-100/75 hover:bg-white/5 hover:text-white';
@endphp

{{-- Backdrop mobile --}}
<div x-show="navOpen" x-cloak x-transition.opacity
     class="fixed inset-0 z-30 bg-ink/50 lg:hidden" @click="navOpen = false" aria-hidden="true"></div>

<aside class="fixed inset-y-0 left-0 z-40 flex w-64 -translate-x-full flex-col bg-brand-900 transition-transform duration-200 ease-out lg:translate-x-0"
       :class="navOpen && 'translate-x-0'" aria-label="Navigasi utama">

    {{-- Identitas aplikasi --}}
    <div class="flex items-center gap-3 px-5 pb-5 pt-6">
        <x-application-logo class="h-9 w-9 shrink-0 rounded-lg bg-white p-1.5" />
        <div class="min-w-0">
            <p class="font-display text-base font-bold leading-tight text-white">CRM Retur</p>
            <p class="text-[11px] font-medium uppercase tracking-wider text-brand-300">Panel Operasional</p>
        </div>
        <button type="button" @click="navOpen = false" aria-label="Tutup menu"
                class="ml-auto rounded-lg p-1.5 text-brand-200 hover:bg-white/10 hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-white/60 lg:hidden">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" d="M6 6l12 12M18 6L6 18" />
            </svg>
        </button>
    </div>

    <nav class="flex-1 space-y-6 overflow-y-auto px-3 pb-4">
        {{-- Operasi harian --}}
        <div>
            <p class="px-3 pb-2 text-[11px] font-semibold uppercase tracking-wider text-brand-300/80">Operasi</p>
            <div class="space-y-1">
                <a href="{{ route('staff.dashboard') }}"
                   class="{{ $navItem }} {{ request()->routeIs('staff.dashboard') ? $navActive : $navIdle }}">
                    <svg class="h-[18px] w-[18px] shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <rect x="3.5" y="3.5" width="7" height="7" rx="1.5" /><rect x="13.5" y="3.5" width="7" height="7" rx="1.5" />
                        <rect x="3.5" y="13.5" width="7" height="7" rx="1.5" /><rect x="13.5" y="13.5" width="7" height="7" rx="1.5" />
                    </svg>
                    Dasbor
                </a>
                <a href="{{ route('staff.tickets.index') }}"
                   class="{{ $navItem }} {{ request()->routeIs('staff.tickets.*') ? $navActive : $navIdle }}">
                    <svg class="h-[18px] w-[18px] shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.5 9.5V6.75A2.25 2.25 0 0 1 5.75 4.5h12.5a2.25 2.25 0 0 1 2.25 2.25V9.5a2.5 2.5 0 0 0 0 5v2.75a2.25 2.25 0 0 1-2.25 2.25H5.75a2.25 2.25 0 0 1-2.25-2.25V14.5a2.5 2.5 0 0 0 0-5Z" />
                        <path stroke-linecap="round" stroke-dasharray="2.5 3" d="M13.5 4.5v15" />
                    </svg>
                    Tiket Retur
                </a>
            </div>
        </div>
        {{-- Administrasi: Admin & Manajemen lengkap; Customer Service mendapat FAQ (paritas lawas) --}}
        @if (auth()->user()->hasRole(Role::Admin, Role::Manajemen, Role::CustomerService))
            <div>
                <p class="px-3 pb-2 text-[11px] font-semibold uppercase tracking-wider text-brand-300/80">Administrasi</p>
                <div class="space-y-1">
                    @if (auth()->user()->hasRole(Role::Admin, Role::Manajemen))
                        <a href="{{ route('admin.analytics') }}"
                           class="{{ $navItem }} {{ request()->routeIs('admin.analytics') ? $navActive : $navIdle }}">
                            <svg class="h-[18px] w-[18px] shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path stroke-linecap="round" d="M4 20v-9M9.5 20V4M15 20v-6M20.5 20V9M2.5 20h19" />
                            </svg>
                            Analitik
                        </a>
                    @endif
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('admin.users.index') }}"
                           class="{{ $navItem }} {{ request()->routeIs('admin.users.*') ? $navActive : $navIdle }}">
                            <svg class="h-[18px] w-[18px] shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <circle cx="9.5" cy="8" r="3.25" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 19.5v-1.4a3.6 3.6 0 0 1 3.6-3.6h4.3a3.6 3.6 0 0 1 3.6 3.6v1.4" />
                                <path stroke-linecap="round" d="M16 5.2a3.25 3.25 0 0 1 0 5.6M17.7 14.6a3.6 3.6 0 0 1 2.55 3.4v1.5" />
                            </svg>
                            Pengguna
                        </a>
                        <a href="{{ route('admin.products.index') }}"
                           class="{{ $navItem }} {{ request()->routeIs('admin.products.*') ? $navActive : $navIdle }}">
                            <svg class="h-[18px] w-[18px] shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3.2 4.5 7.35v9.3L12 20.8l7.5-4.15v-9.3L12 3.2Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 7.35 12 11.5l7.5-4.15M12 11.5v9.3" />
                            </svg>
                            Produk
                        </a>
                    @endif
                    <a href="{{ route('admin.faqs.index') }}"
                       class="{{ $navItem }} {{ request()->routeIs('admin.faqs.*') ? $navActive : $navIdle }}">
                        <svg class="h-[18px] w-[18px] shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <circle cx="12" cy="12" r="8.5" />
                            <path stroke-linecap="round" d="M9.9 9.4a2.2 2.2 0 1 1 3.3 1.9c-.7.45-1.2.95-1.2 1.8" />
                            <path stroke-linecap="round" d="M12 15.9h.01" />
                        </svg>
                        FAQ
                    </a>
                </div>
            </div>
        @endif
    </nav>

    {{-- Blok bawah: tautan portal + pengguna aktif --}}
    <div class="border-t border-white/10 px-3 py-3">
        <a href="{{ route('portal.home') }}" class="{{ $navItem }} {{ $navIdle }} mb-2">
            <svg class="h-[18px] w-[18px] shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6.5H7A2.5 2.5 0 0 0 4.5 9v8A2.5 2.5 0 0 0 7 19.5h8A2.5 2.5 0 0 0 17.5 17v-6.5M19.5 4.5 12 12m7.5-7.5h-5m5 0v5" />
            </svg>
            Portal Pelanggan
        </a>
        <div class="flex items-center gap-3 rounded-lg px-2 py-1.5">
            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-brand-500 text-xs font-bold uppercase text-white">
                {{ collect(explode(' ', auth()->user()->name))->filter()->map(fn ($word) => mb_substr($word, 0, 1))->take(2)->implode('') }}
            </span>
            <div class="min-w-0 flex-1">
                <p class="truncate text-sm font-semibold text-white">{{ auth()->user()->name }}</p>
                <p class="truncate text-[11px] text-brand-200/80">{{ auth()->user()->role?->label() ?? 'Staf' }}</p>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" title="Keluar" aria-label="Keluar"
                        class="rounded-lg p-2 text-brand-200 hover:bg-white/10 hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-white/60">
                    <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.5 15.5v2.75c0 .97.78 1.75 1.75 1.75h7c.97 0 1.75-.78 1.75-1.75V5.75c0-.97-.78-1.75-1.75-1.75h-7c-.97 0-1.75.78-1.75 1.75v2.75M3 12h10.5m0 0L10 8.5m3.5 3.5L10 15.5" />
                    </svg>
                </button>
            </form>
        </div>
    </div>
</aside>