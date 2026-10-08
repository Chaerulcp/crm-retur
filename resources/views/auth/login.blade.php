<x-guest-layout>
    <div class="flex min-h-screen bg-paper">
        {{-- Panel identitas --}}
        <div class="relative hidden w-[45%] flex-col justify-between overflow-hidden bg-brand-900 p-10 lg:flex">
            <svg aria-hidden="true" class="pointer-events-none absolute inset-0 h-full w-full opacity-[0.12]" viewBox="0 0 600 800" fill="none" preserveAspectRatio="xMidYMid slice">
                <path d="M-60 640 C 120 480, 300 660, 460 480 S 700 380, 760 460" stroke="#8FBCBC" stroke-width="2" stroke-dasharray="6 10" />
                <path d="M-60 300 C 140 180, 360 340, 560 160" stroke="#5C9A9C" stroke-width="2" stroke-dasharray="2 8" />
                <circle cx="460" cy="480" r="5" fill="#8FBCBC" />
            </svg>

            <div class="relative flex items-center gap-3">
                <x-application-logo class="h-10 w-10 rounded-lg bg-white p-1.5" />
                <div>
                    <p class="font-display text-lg font-bold text-white">CRM Retur</p>
                    <p class="text-[11px] font-medium uppercase tracking-wider text-brand-300">Panel Operasional</p>
                </div>
            </div>

            <div class="relative">
                <p class="font-display text-3xl font-bold leading-snug text-white">
                    Setiap retur punya jalur.<br>Anda mengelola jalurnya.
                </p>
                <p class="mt-4 max-w-sm text-sm leading-6 text-brand-100">
                    Pantau pengajuan, verifikasi, pemeriksaan gudang, dan refund dalam satu tempat.
                </p>
            </div>

            <p class="relative text-xs text-brand-300">Masuk dengan akun yang diberikan admin.</p>
        </div>

        {{-- Panel formulir --}}
        <div class="flex flex-1 items-center justify-center px-4 py-12 sm:px-6">
            <div class="w-full max-w-md">
                <div class="mb-8 flex items-center gap-3 lg:hidden">
                    <x-application-logo class="h-10 w-10" />
                    <div>
                        <p class="font-display text-lg font-bold text-ink">CRM Retur</p>
                        <p class="text-[11px] font-medium uppercase tracking-wider text-brand-600">Panel Operasional</p>
                    </div>
                </div>

                <p class="eyebrow">Masuk</p>
                <h1 class="mt-2 font-display text-2xl font-bold text-ink">Selamat datang kembali</h1>
                <p class="mt-1.5 text-sm text-slate-500">Gunakan email dan kata sandi akun staf Anda.</p>

                <div class="card mt-6 p-6 sm:p-7">
                    <x-auth-session-status class="mb-4" :status="session('status')" />

                    <form method="POST" action="{{ route('login') }}" class="space-y-5">
                        @csrf

                        <div>
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" class="mt-1.5 block w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="nama@perusahaan.com" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="password" :value="__('Kata Sandi')" />
                            <x-text-input id="password" class="mt-1.5 block w-full" type="password" name="password" required autocomplete="current-password" />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-between">
                            <label for="remember_me" class="inline-flex items-center gap-2">
                                <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-brand-600 shadow-sm focus:ring-brand-500" name="remember">
                                <span class="text-sm text-slate-600">{{ __('Ingat saya') }}</span>
                            </label>
                            @if (Route::has('password.request'))
                                <a class="text-sm font-medium text-brand-600 hover:text-brand-700" href="{{ route('password.request') }}">
                                    {{ __('Lupa kata sandi?') }}
                                </a>
                            @endif
                        </div>

                        <x-primary-button class="w-full justify-center">
                            {{ __('Masuk ke Panel') }}
                        </x-primary-button>
                    </form>
                </div>

                <p class="mt-6 text-center text-xs text-slate-400">
                    Bukan staf? Pelanggan dapat mengajukan retur tanpa akun di
                    <a href="{{ route('portal.home') }}" class="font-medium text-brand-600 hover:text-brand-700">Portal Pelanggan</a>.
                </p>
            </div>
        </div>
    </div>
</x-guest-layout>