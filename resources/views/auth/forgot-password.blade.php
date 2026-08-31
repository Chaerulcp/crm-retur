<x-guest-layout>
    <div class="flex min-h-screen items-center justify-center bg-paper px-4 py-12">
        <div class="w-full max-w-md">
            <div class="mb-6 flex items-center gap-3">
                <x-application-logo class="h-10 w-10" />
                <div>
                    <p class="font-display text-lg font-bold text-ink">CRM Retur</p>
                    <p class="text-[11px] font-medium uppercase tracking-wider text-brand-600">Panel Operasional</p>
                </div>
            </div>

            <div class="card p-6 sm:p-7">
                <h1 class="font-display text-xl font-bold text-ink">Lupa kata sandi?</h1>
                <p class="mt-2 text-sm leading-6 text-slate-500">
                    Masukkan email Anda dan kami akan mengirimkan tautan untuk mengatur kata sandi baru.
                </p>

                <x-auth-session-status class="mt-4" :status="session('status')" />

                <form method="POST" action="{{ route('password.email') }}" class="mt-5 space-y-4">
                    @csrf

                    <div>
                        <x-input-label for="email" :value="__('Email')" />
                        <x-text-input id="email" class="mt-1.5 block w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <x-primary-button class="w-full justify-center">Kirim Tautan Reset</x-primary-button>
                </form>
            </div>

            <p class="mt-6 text-center text-xs text-slate-400">
                <a href="{{ route('login') }}" class="font-medium text-brand-600 hover:text-brand-700">Kembali ke halaman masuk</a>
            </p>
        </div>
    </div>
</x-guest-layout>
