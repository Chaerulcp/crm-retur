<x-guest-layout>
    <div class="flex min-h-screen items-center justify-center bg-paper px-4 py-12">
        <div class="w-full max-w-md">
            <div class="card p-6 sm:p-7">
                <h1 class="font-display text-xl font-bold text-ink">Verifikasi email Anda</h1>
                <p class="mt-2 text-sm leading-6 text-slate-500">
                    Terima kasih sudah mendaftar! Klik tautan verifikasi yang baru kami kirim ke email Anda. Tidak menerima email? Kami kirim ulang.
                </p>

                @if (session('status') == 'verification-link-sent')
                    <div class="flash-success mt-4">
                        Tautan verifikasi baru telah dikirim ke alamat email Anda.
                    </div>
                @endif

                <div class="mt-5 flex flex-wrap items-center justify-between gap-3">
                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <x-primary-button>Kirim Ulang Email</x-primary-button>
                    </form>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm font-medium text-slate-500 hover:text-ink">
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
