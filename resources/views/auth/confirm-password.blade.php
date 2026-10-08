<x-guest-layout>
    <div class="flex min-h-screen items-center justify-center bg-paper px-4 py-12">
        <div class="w-full max-w-md">
            <div class="card p-6 sm:p-7">
                <h1 class="font-display text-xl font-bold text-ink">Area aman</h1>
                <p class="mt-2 text-sm leading-6 text-slate-500">
                    Ini area aman aplikasi. Konfirmasi kata sandi Anda sebelum melanjutkan.
                </p>

                <form method="POST" action="{{ route('password.confirm') }}" class="mt-5 space-y-4">
                    @csrf

                    <div>
                        <x-input-label for="password" :value="__('Kata Sandi')" />
                        <x-text-input id="password" class="mt-1.5 block w-full" type="password" name="password" required autocomplete="current-password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <x-primary-button class="w-full justify-center">Konfirmasi</x-primary-button>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
