<x-guest-layout>
    <div class="flex min-h-screen flex-col bg-paper">
        @include('portal.partials.nav')

        <main class="mx-auto w-full max-w-xl flex-1 px-4 py-16 sm:px-6">
            <p class="eyebrow">Pelacakan</p>
            <h1 class="mt-2 font-display text-2xl font-bold text-ink sm:text-3xl">Lacak Retur Anda</h1>
            <p class="mt-2 text-sm leading-6 text-slate-600">
                Masukkan nomor retur yang Anda terima saat mengajukan, contoh:
                <span class="font-mono text-xs">RET-20260115-0001</span>.
            </p>

            <div class="card mt-8 p-6 sm:p-7">
                <form method="GET" action="{{ route('portal.tracking') }}">
                    <x-input-label for="nomor" value="Nomor Retur" />
                    <div class="mt-1.5 flex flex-col gap-2 sm:flex-row">
                        <input id="nomor" type="text" name="nomor" required value="{{ old('nomor') }}"
                               placeholder="RET-YYYYMMDD-XXXX" autocomplete="off"
                               class="input flex-1 font-mono uppercase tracking-wide placeholder:font-sans placeholder:normal-case">
                        <x-primary-button class="shrink-0">Cari Tiket</x-primary-button>
                    </div>
                    <x-input-error :messages="$errors->get('nomor')" class="mt-2" />
                    <p class="hint">Nomor dikirim lewat email setelah pengajuan dan juga tampil di halaman konfirmasi.</p>
                </form>
            </div>
        </main>

        @include('portal.partials.footer')
    </div>
</x-guest-layout>