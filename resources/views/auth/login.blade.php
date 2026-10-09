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
            <h2 class="mt-6 text-center text-2xl font-bold leading-9 tracking-tight text-ink">Log in to your account</h2>
        </div>

        <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-[480px]">
            <div class="bg-white px-6 py-12 shadow-xl shadow-slate-200/40 sm:rounded-2xl sm:px-12 border border-slate-100">
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    <div>
                        <label for="email" class="block text-sm font-medium leading-6 text-ink">Email address</label>
                        <div class="mt-2">
                            <input id="email" name="email" type="email" autocomplete="email" required class="block w-full rounded-lg border-0 py-2.5 text-ink shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-ink sm:text-sm sm:leading-6" value="{{ old('email') }}">
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div>
                        <div class="flex items-center justify-between">
                            <label for="password" class="block text-sm font-medium leading-6 text-ink">Password</label>
                            @if (Route::has('password.request'))
                                <div class="text-sm">
                                    <a href="{{ route('password.request') }}" class="font-medium text-slate-500 hover:text-ink">Forgot password?</a>
                                </div>
                            @endif
                        </div>
                        <div class="mt-2">
                            <input id="password" name="password" type="password" autocomplete="current-password" required class="block w-full rounded-lg border-0 py-2.5 text-ink shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-ink sm:text-sm sm:leading-6">
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div class="flex items-center">
                        <input id="remember_me" name="remember" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-ink focus:ring-ink">
                        <label for="remember_me" class="ml-3 block text-sm leading-6 text-slate-600">Remember me</label>
                    </div>

                    <div>
                        <button type="submit" class="flex w-full justify-center rounded-lg bg-ink px-3 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-slate-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-ink transition-colors">
                            Log in
                        </button>
                    </div>
                </form>
            </div>
            
            <p class="mt-8 text-center text-sm text-slate-500">
                Don't have an account?
                <a href="{{ route('register') }}" class="font-medium leading-6 text-ink hover:underline">Start a 14-day free trial</a>
            </p>
        </div>
    </div>
</x-guest-layout>