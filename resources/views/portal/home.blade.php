<x-guest-layout>
    <div class="flex min-h-screen flex-col bg-paper">
        @include('portal.partials.nav')

        <main class="flex-1">
            {{-- Hero: judul + pelacakan nomor retur sebagai pusat halaman --}}
            <section class="relative overflow-hidden bg-brand-900">
                <svg aria-hidden="true" class="pointer-events-none absolute inset-0 h-full w-full opacity-[0.12]" viewBox="0 0 800 400" fill="none" preserveAspectRatio="xMidYMid slice">
                    <path d="M-40 320 C 180 140, 420 380, 620 180 S 900 120, 980 220" stroke="#8FBCBC" stroke-width="2" stroke-dasharray="6 10" />
                    <path d="M-40 200 C 160 60, 480 260, 720 80" stroke="#5C9A9C" stroke-width="2" stroke-dasharray="2 8" />
                    <circle cx="620" cy="180" r="5" fill="#8FBCBC" />
                    <circle cx="180" cy="235" r="4" fill="#5C9A9C" />
                </svg>

                <div class="relative mx-auto grid max-w-5xl items-center gap-10 px-4 py-14 sm:px-6 lg:grid-cols-[1fr_minmax(0,420px)] lg:py-20">
                    <div>
                        <p class="eyebrow text-brand-300">Portal Retur Pelanggan</p>
                        <h1 class="mt-3 font-display text-3xl font-bold leading-tight text-white sm:text-4xl lg:text-[2.75rem]">
                            Retur barang tanpa<br class="hidden sm:block"> bolak-balik bertanya.
                        </h1>
                        <p class="mt-4 max-w-lg text-base leading-7 text-brand-100">
                            Ajukan pengembalian dalam hitungan menit, lalu ikuti setiap tahapnya &mdash;
                            verifikasi, pengiriman, pemeriksaan gudang, sampai refund &mdash; tanpa perlu membuat akun.
                        </p>
                        <div class="mt-7 flex flex-wrap gap-3">
                            <a href="{{ route('portal.create') }}"
                               class="inline-flex items-center gap-2 rounded-lg bg-white px-5 py-2.5 text-sm font-semibold text-brand-800 shadow-sm transition hover:bg-brand-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white">
                                Ajukan Retur
                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M3 10a.75.75 0 0 1 .75-.75h9.69L10.22 6.03a.75.75 0 1 1 1.06-1.06l4.5 4.5a.75.75 0 0 1 0 1.06l-4.5 4.5a.75.75 0 1 1-1.06-1.06l3.22-3.22H3.75A.75.75 0 0 1 3 10Z" clip-rule="evenodd"/></svg>
                            </a>
                            <a href="{{ route('portal.faq') }}"
                               class="inline-flex items-center rounded-lg border border-white/25 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-white/10 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white">
                                Baca FAQ
                            </a>
                        </div>
                    </div>

                    {{-- Kartu lacak: nomor retur adalah identitas khas pengajuan --}}
                    <div class="card p-6 shadow-xl sm:p-7">
                        <p class="eyebrow">Lacak Status Retur</p>
                        <form method="GET" action="{{ route('portal.tracking') }}" class="mt-4">
                            <label for="tracking-number" class="label">Nomor retur</label>
                            <div class="mt-1.5 flex flex-col gap-2 sm:flex-row">
                                <input id="tracking-number" type="text" name="nomor" required
                                       placeholder="RET-YYYYMMDD-XXXX" autocomplete="off"
                                       class="input flex-1 font-mono uppercase tracking-wide placeholder:font-sans placeholder:normal-case">
                                <button type="submit" class="btn-dark shrink-0">Lacak</button>
                            </div>
                            <p class="hint">Nomor ini Anda terima setelah pengajuan, contoh: <span class="font-mono">RET-20260830-0015</span>.</p>
                        </form>
                    </div>
                </div>
            </section>
            {{-- Tiga tahap proses — urutan yang sesungguhnya --}}
            <section class="mx-auto max-w-5xl px-4 py-14 sm:px-6">
                <p class="eyebrow">Proses Retur</p>
                <h2 class="mt-2 font-display text-2xl font-bold text-ink">Tiga langkah sampai refund</h2>

                <ol class="mt-8 grid gap-6 sm:grid-cols-3">
                    @foreach ([
                        ['n' => '01', 'judul' => 'Isi Formulir', 'isi' => 'Lengkapi data diri, produk, dan alasan retur beserta foto atau video bukti pendukung.'],
                        ['n' => '02', 'judul' => 'Verifikasi &amp; Persetujuan', 'isi' => 'Tim kami memeriksa pengajuan Anda dan memberikan keputusan, biasanya dalam 1&ndash;2 hari kerja.'],
                        ['n' => '03', 'judul' => 'Kirim Barang &amp; Terima Refund', 'isi' => 'Kirim barang ke gudang kami. Setelah pemeriksaan selesai, refund dikirim sesuai metode pilihan Anda.'],
                    ] as $step)
                        <li class="card relative p-6">
                            <span class="font-mono text-sm font-semibold text-brand-500">{{ $step['n'] }}</span>
                            <h3 class="mt-2 font-display text-base font-bold text-ink">{!! $step['judul'] !!}</h3>
                            <p class="mt-2 text-sm leading-6 text-slate-600">{!! $step['isi'] !!}</p>
                        </li>
                    @endforeach
                </ol>
            </section>

            {{-- Komitmen layanan --}}
            <section class="border-y border-slate-200 bg-white">
                <div class="mx-auto grid max-w-5xl gap-6 px-4 py-12 sm:grid-cols-3 sm:px-6">
                    <div class="flex gap-3.5">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-brand-50 text-brand-700">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5c-5 0-8.5 4-9.5 7 1 3 4.5 7 9.5 7s8.5-4 9.5-7c-1-3-4.5-7-9.5-7Z"/><circle cx="12" cy="12" r="2.75"/></svg>
                        </span>
                        <div>
                            <h3 class="text-sm font-semibold text-ink">Status transparan</h3>
                            <p class="mt-1 text-sm leading-6 text-slate-600">Setiap perubahan status tercatat dan bisa Anda pantau kapan saja lewat nomor retur.</p>
                        </div>
                    </div>
                    <div class="flex gap-3.5">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-brand-50 text-brand-700">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 7.5 12 12l7.5-4.5M4.5 7.5v9A1.5 1.5 0 0 0 6 18h12a1.5 1.5 0 0 0 1.5-1.5v-9M4.5 7.5 12 3.75 19.5 7.5"/></svg>
                        </span>
                        <div>
                            <h3 class="text-sm font-semibold text-ink">Notifikasi email</h3>
                            <p class="mt-1 text-sm leading-6 text-slate-600">Konfirmasi pengajuan dan setiap pembaruan penting dikirim otomatis ke email Anda.</p>
                        </div>
                    </div>
                    <div class="flex gap-3.5">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-brand-50 text-brand-700">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 19.5h9A4.5 4.5 0 0 0 21 15a4.5 4.5 0 0 0-4.5-4.5h-9A4.5 4.5 0 0 0 3 15c0 1.86 1.1 3.45 2.68 4.16L5 21.5l2.5-2Z"/></svg>
                        </span>
                        <div>
                            <h3 class="text-sm font-semibold text-ink">Live chat dengan staf</h3>
                            <p class="mt-1 text-sm leading-6 text-slate-600">Ada pertanyaan di tengah proses? Chat langsung dengan tim kami dari halaman lacak.</p>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Ajakan akhir --}}
            <section class="mx-auto max-w-5xl px-4 py-14 text-center sm:px-6">
                <h2 class="font-display text-2xl font-bold text-ink">Siap mengajukan retur?</h2>
                <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-600">
                    Formulirnya singkat. Siapkan nomor invoice (jika ada) dan foto kondisi barang agar proses lebih cepat.
                </p>
                <a href="{{ route('portal.create') }}" class="btn-primary mt-6 px-6">Ajukan Retur Sekarang</a>
            </section>
        </main>

        @include('portal.partials.footer')
    </div>
</x-guest-layout>