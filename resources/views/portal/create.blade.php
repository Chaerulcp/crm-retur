<x-guest-layout>
    <div class="flex min-h-screen flex-col bg-paper">
        @include('portal.partials.nav')

        <main class="mx-auto w-full max-w-3xl flex-1 px-4 py-10 sm:px-6">
            <p class="eyebrow">Formulir Pengajuan</p>
            <h1 class="mt-2 font-display text-2xl font-bold text-ink sm:text-3xl">Ajukan Retur</h1>
            <p class="mt-2 text-sm leading-6 text-slate-600">
                Lengkapi formulir di bawah ini. Kolom bertanda <span class="text-red-500">*</span> wajib diisi.
            </p>

            <form method="POST" action="{{ route('portal.store') }}" enctype="multipart/form-data" class="mt-8 space-y-8">
                @csrf

                {{-- Data pelanggan --}}
                <section class="card p-6 sm:p-7">
                    <h2 class="font-display text-base font-bold text-ink">Data Diri</h2>
                    <div class="mt-5 space-y-5">
                        <div>
                            <x-input-label for="customer_name" value="Nama Lengkap *" />
                            <x-text-input id="customer_name" name="customer_name" type="text" class="mt-1.5 block w-full"
                                :value="old('customer_name')" required autofocus autocomplete="name" placeholder="Nama sesuai pemesanan" />
                            <x-input-error :messages="$errors->get('customer_name')" class="mt-2" />
                        </div>

                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <x-input-label for="email" value="Alamat Email *" />
                                <x-text-input id="email" name="email" type="email" class="mt-1.5 block w-full"
                                    :value="old('email')" required autocomplete="email" placeholder="nama@email.com" />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="phone" value="Nomor Telepon (opsional)" />
                                <x-text-input id="phone" name="phone" type="text" class="mt-1.5 block w-full"
                                    :value="old('phone')" placeholder="081234567890" autocomplete="tel" />
                                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                            </div>
                        </div>
                    </div>
                </section>
                {{-- Produk & alasan --}}
                <section class="card p-6 sm:p-7">
                    <h2 class="font-display text-base font-bold text-ink">Produk &amp; Alasan Retur</h2>
                    <div class="mt-5 space-y-5">
                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <x-input-label for="product_id" value="Produk yang Diretur *" />
                                <select id="product_id" name="product_id" required class="input mt-1.5 block w-full">
                                    <option value="">Pilih produk...</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}" @selected(old('product_id') == $product->id)>{{ $product->name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('product_id')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="invoice_number" value="Nomor Invoice (opsional)" />
                                <x-text-input id="invoice_number" name="invoice_number" type="text" class="mt-1.5 block w-full font-mono uppercase placeholder:font-sans placeholder:normal-case"
                                    :value="old('invoice_number')" placeholder="INV-10021" />
                                <x-input-error :messages="$errors->get('invoice_number')" class="mt-2" />
                            </div>
                        </div>

                        <div>
                            <x-input-label for="reason" value="Alasan Retur *" />
                            <textarea id="reason" name="reason" rows="4" required
                                placeholder="Jelaskan kondisi barang dan alasan pengajuan retur"
                                class="input mt-1.5 block w-full">{{ old('reason') }}</textarea>
                            <x-input-error :messages="$errors->get('reason')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="refund_method" value="Metode Refund Pilihan (opsional)" />
                            <select id="refund_method" name="refund_method" class="input mt-1.5 block w-full">
                                <option value="">Dipilih nanti</option>
                                @foreach (['Transfer Bank', 'E-Wallet', 'Store Credit'] as $method)
                                    <option value="{{ $method }}" @selected(old('refund_method') === $method)>{{ $method }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('refund_method')" class="mt-2" />
                        </div>
                    </div>
                </section>

                {{-- Bukti pendukung --}}
                <section class="card p-6 sm:p-7">
                    <h2 class="font-display text-base font-bold text-ink">Bukti Pendukung</h2>
                    <p class="mt-1 text-sm text-slate-500">Foto atau video kondisi barang mempercepat proses verifikasi.</p>
                    <div class="mt-4">
                        <label for="evidences" class="flex cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed border-slate-300 bg-slate-50 px-6 py-10 text-center transition hover:border-brand-400 hover:bg-brand-50/50">
                            <svg class="h-8 w-8 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5"/>
                            </svg>
                            <span class="mt-3 text-sm font-semibold text-brand-600">Klik untuk memilih berkas</span>
                            <span class="mt-1 text-xs text-slate-400">Maks. 5 berkas &mdash; gambar s.d. 5 MB, video s.d. 50 MB per berkas</span>
                            <input id="evidences" name="evidences[]" type="file" multiple accept="image/*,video/*" class="sr-only">
                        </label>
                        <x-input-error :messages="$errors->get('evidences')" class="mt-2" />
                        @for ($i = 0; $i < 5; $i++)
                            <x-input-error :messages="$errors->get(\"evidences.$i\")" class="mt-2" />
                        @endfor
                    </div>
                </section>

                {{-- Verifikasi CAPTCHA (hanya tampil bila sitekey dikonfigurasi) --}}
                @if (config('services.recaptcha.sitekey'))
                    <section class="card p-6 sm:p-7">
                        <h2 class="font-display text-base font-bold text-ink">Verifikasi</h2>
                        <p class="mt-1 text-sm text-slate-500">Centang kotak di bawah untuk memastikan Anda bukan robot.</p>
                        <div class="mt-4">
                            <script src="https://www.google.com/recaptcha/api.js" async defer></script>
                            <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.sitekey') }}"></div>
                            <x-input-error :messages="$errors->get('g-recaptcha-response')" class="mt-2" />
                        </div>
                    </section>
                @endif

                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('portal.home') }}" class="text-sm font-medium text-slate-500 hover:text-ink">Batal</a>
                    <x-primary-button class="px-6">Ajukan Retur</x-primary-button>
                </div>
            </form>
        </main>

        @include('portal.partials.footer')
    </div>
</x-guest-layout>