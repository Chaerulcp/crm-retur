<x-guest-layout>
    <div class="flex min-h-screen items-center justify-center bg-paper px-4 py-12">
        <div class="w-full max-w-md">
            <div class="card p-6 sm:p-7">
                <h1 class="font-display text-xl font-bold text-ink">Atur kata sandi baru</h1>

                <form method="POST" action="{{ route('password.store') }}" class="mt-5 space-y-4">
                    @csrf

                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <div>
                        <x-input-label for="email" :value="__('Email')" />
                        <x-text-input id="email" class="mt-1.5 block w-full" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="password" :value="__('Kata Sandi Baru')" />
                        <x-text-input id="password" class="mt-1.5 block w-full" type="password" name="password" required autocomplete="new-password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="password_confirmation" :value="__('Konfirmasi Kata Sandi')" />
                        <x-text-input id="password_confirmation" class="mt-1.5 block w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>

                    <x-primary-button class="w-full justify-center">Simpan Kata Sandi</x-primary-button>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
