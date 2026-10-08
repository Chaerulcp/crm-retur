@props(['title', 'subtitle' => null, 'action', 'method' => 'POST', 'submitLabel', 'cancelHref'])

{{-- Kerangka halaman formulir admin: judul + kartu form dengan tombol batal/simpan. --}}
<x-app-layout>
    <x-slot name="header">
        <p class="eyebrow">Formulir</p>
        <h1 class="mt-1 font-display text-2xl font-bold text-ink">{{ $title }}</h1>
        @if ($subtitle)
            <p class="mt-1 text-sm text-slate-500">{{ $subtitle }}</p>
        @endif
    </x-slot>

    <div class="mx-auto max-w-3xl">
        <div class="card p-6 sm:p-7">
            <form method="POST" action="{{ $action }}" class="space-y-5">
                @csrf
                @if (strtoupper($method) !== 'POST')
                    @method($method)
                @endif

                {{ $slot }}

                <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-5">
                    <a href="{{ $cancelHref }}" class="text-sm font-medium text-slate-500 hover:text-ink">Batal</a>
                    <x-primary-button>{{ $submitLabel }}</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>