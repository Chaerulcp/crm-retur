<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'CRM Retur') }}</title>

        {{-- Tipografi: Bricolage Grotesque (display), IBM Plex Sans (isi), IBM Plex Mono (data) --}}
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=bricolage-grotesque:500,600,700|ibm-plex-mono:400,500,600|ibm-plex-sans:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-paper font-sans text-ink antialiased">
        <div x-data="{ navOpen: false }" class="min-h-screen lg:pl-64">
            {{-- Sidebar navigasi (drawer di layar kecil) --}}
            @include('layouts.navigation')

            {{-- Bilah atas: menu mobile + pengguna --}}
            <header class="sticky top-0 z-20 border-b border-slate-200 bg-white/90 backdrop-blur">
                <div class="flex h-14 items-center gap-3 px-4 sm:px-6 lg:px-8">
                    <button type="button" @click="navOpen = true"
                            class="inline-flex items-center justify-center rounded-lg p-2 text-slate-500 hover:bg-slate-100 hover:text-slate-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 lg:hidden"
                            aria-label="Buka menu navigasi">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16" />
                        </svg>
                    </button>

                    <span class="font-display text-sm font-semibold text-slate-700 lg:hidden">CRM Retur</span>

                    <div class="flex-1"></div>

                    <x-dropdown align="right" width="56">
                        <x-slot name="trigger">
                            <button class="flex items-center gap-2 rounded-lg px-2 py-1.5 text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500">
                                <span class="flex h-7 w-7 items-center justify-center rounded-full bg-brand-600 text-[11px] font-bold uppercase text-white">
                                    {{ collect(explode(' ', auth()->user()->name))->filter()->map(fn ($word) => mb_substr($word, 0, 1))->take(2)->implode('') }}
                                </span>
                                <span class="hidden max-w-[10rem] truncate sm:block">{{ auth()->user()->name }}</span>
                                <svg class="h-4 w-4 text-slate-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <div class="border-b border-slate-100 px-4 py-2.5">
                                <p class="text-sm font-semibold text-ink">{{ auth()->user()->name }}</p>
                                <p class="mt-0.5 text-xs text-slate-500">{{ auth()->user()->email }}</p>
                                <span class="mt-1.5 inline-block rounded-full bg-brand-50 px-2 py-0.5 text-[11px] font-semibold text-brand-700">
                                    {{ auth()->user()->role?->label() ?? 'Staf' }}
                                </span>
                            </div>
                            <x-dropdown-link :href="route('profile.edit')">Profil Saya</x-dropdown-link>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault(); this.closest('form').submit();">
                                    Keluar
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            </header>

            {{-- Isi halaman --}}
            <main class="px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
                <div class="mx-auto max-w-7xl space-y-6">
                    @isset($header)
                        <div>
                            {{ $header }}
                        </div>
                    @endisset

                    {{ $slot }}
                </div>
            </main>
        </div>
    </body>
</html>
