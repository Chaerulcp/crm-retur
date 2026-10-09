<x-guest-layout>
    <div class="flex min-h-screen flex-col bg-slate-50 font-sans text-slate-900 selection:bg-brand-100 selection:text-brand-900">
        @include('portal.partials.nav')

        <main class="mx-auto w-full max-w-lg flex-1 px-4 py-16 sm:px-6 flex flex-col justify-center">
            <div class="text-center mb-8">
                <h1 class="font-display text-3xl font-bold tracking-tight text-slate-900">Lacak Retur</h1>
                <p class="mt-2 text-base text-slate-600">
                    Masukkan nomor tiket (Resi Retur) Anda untuk mengecek status terkini pengajuan pengembalian.
                </p>
            </div>

            <div class="overflow-hidden rounded-xl bg-white shadow-sm border border-slate-200">
                <div class="p-8">
                    <form action="{{ route('portal.tracking.search') }}" method="POST" class="space-y-6">
                        @csrf
                        <div>
                            <label for="ticket_number" class="block text-sm font-medium leading-6 text-slate-900">Nomor Tiket</label>
                            <div class="mt-2">
                                <input type="text" id="ticket_number" name="ticket_number" required
                                    placeholder="Contoh: RET-123456"
                                    class="block w-full rounded-lg border-0 py-3 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-slate-900 sm:text-base sm:leading-6 font-mono uppercase">
                            </div>
                            <x-input-error :messages="$errors->get('ticket_number')" class="mt-2" />
                            @if(session('error'))
                                <p class="mt-2 text-sm text-red-600">{{ session('error') }}</p>
                            @endif
                        </div>
                        
                        <button type="submit" class="flex w-full justify-center rounded-lg bg-slate-900 px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-slate-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-slate-900 transition-colors">
                            Cek Status Sekarang
                        </button>
                    </form>
                </div>
            </div>
            
            <p class="mt-8 text-center text-sm text-slate-500">
                Lupa nomor tiket Anda? Silakan periksa email masuk atau folder spam Anda.
            </p>
        </main>

        @include('portal.partials.footer')
    </div>
</x-guest-layout>