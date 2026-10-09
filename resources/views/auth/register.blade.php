<x-guest-layout>
    <div class="flex min-h-screen flex-col justify-center bg-slate-50 py-12 sm:px-6 lg:px-8 selection:bg-ink selection:text-white">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <a href="/" class="flex justify-center items-center gap-2 mb-6">
                <svg class="h-8 w-8 text-ink" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                </svg>
                <span class="font-display text-2xl font-bold tracking-tight text-ink">Retunly</span>
            </a>
            <h2 class="mt-6 text-center text-2xl font-bold leading-9 tracking-tight text-ink">Create your account</h2>
            <p class="mt-2 text-center text-sm text-slate-500">
                Start your 14-day free trial. No credit card required.
            </p>
        </div>

        <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-[480px]">
            <div class="bg-white px-6 py-12 shadow-xl shadow-slate-200/40 sm:rounded-2xl sm:px-12 border border-slate-100">
                <form method="POST" action="{{ route('register') }}" class="space-y-6">
                    @csrf

                    <div>
                        <label for="name" class="block text-sm font-medium leading-6 text-ink">Full name</label>
                        <div class="mt-2">
                            <input id="name" name="name" type="text" autocomplete="name" required class="block w-full rounded-lg border-0 py-2.5 text-ink shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-ink sm:text-sm sm:leading-6" value="{{ old('name') }}">
                        </div>
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium leading-6 text-ink">Work email</label>
                        <div class="mt-2">
                            <input id="email" name="email" type="email" autocomplete="email" required class="block w-full rounded-lg border-0 py-2.5 text-ink shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-ink sm:text-sm sm:leading-6" value="{{ old('email') }}">
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium leading-6 text-ink">Password</label>
                        <div class="mt-2">
                            <input id="password" name="password" type="password" autocomplete="new-password" required class="block w-full rounded-lg border-0 py-2.5 text-ink shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-ink sm:text-sm sm:leading-6">
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium leading-6 text-ink">Confirm password</label>
                        <div class="mt-2">
                            <input id="password_confirmation" name="password_confirmation" type="password" required class="block w-full rounded-lg border-0 py-2.5 text-ink shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-ink sm:text-sm sm:leading-6">
                        </div>
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>

                    <div>
                        <button type="submit" class="flex w-full justify-center rounded-lg bg-ink px-3 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-slate-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-ink transition-colors">
                            Create account
                        </button>
                    </div>
                </form>
            </div>
            
            <p class="mt-8 text-center text-sm text-slate-500">
                Already have an account?
                <a href="{{ route('login') }}" class="font-medium leading-6 text-ink hover:underline">Log in</a>
            </p>
        </div>
    </div>
</x-guest-layout>
