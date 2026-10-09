<x-guest-layout>
    <div class="flex min-h-screen flex-col bg-slate-50 font-sans text-slate-900 selection:bg-brand-100 selection:text-brand-900">
        @include('portal.partials.nav')

        <main class="mx-auto w-full max-w-2xl flex-1 px-4 py-12 sm:px-6">
            <div class="mb-8">
                <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-600 mb-4 border border-slate-200">
                    Customer Portal
                </span>
                <h1 class="font-display text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">Ajukan Retur</h1>
                <p class="mt-2 text-base text-slate-600">
                    Lengkapi formulir di bawah ini untuk mengajukan pengembalian barang. Kolom bertanda <span class="text-red-500">*</span> wajib diisi.
                </p>
            </div>

            <form method="POST" action="{{ route('portal.store') }}" enctype="multipart/form-data" class="space-y-8">
                @csrf

                {{-- Data pelanggan --}}
                <div class="overflow-hidden rounded-xl bg-white shadow-sm border border-slate-200">
                    <div class="border-b border-slate-200 bg-slate-50/50 px-6 py-4">
                        <h2 class="font-semibold text-slate-900">1. Data Diri</h2>
                    </div>
                    <div class="p-6 space-y-6">
                        <div>
                            <label for="customer_name" class="block text-sm font-medium leading-6 text-slate-900">Nama Lengkap *</label>
                            <div class="mt-2">
                                <input type="text" name="customer_name" id="customer_name" value="{{ old('customer_name') }}" required autofocus autocomplete="name" placeholder="Nama sesuai pesanan" class="block w-full rounded-lg border-0 py-2.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-slate-900 sm:text-sm sm:leading-6">
                            </div>
                            <x-input-error :messages="$errors->get('customer_name')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <label for="email" class="block text-sm font-medium leading-6 text-slate-900">Alamat Email *</label>
                                <div class="mt-2">
                                    <input type="email" name="email" id="email" value="{{ old('email') }}" required autocomplete="email" placeholder="nama@email.com" class="block w-full rounded-lg border-0 py-2.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-slate-900 sm:text-sm sm:leading-6">
                                </div>
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>
                            <div>
                                <label for="phone" class="block text-sm font-medium leading-6 text-slate-900">Nomor Telepon <span class="text-slate-400 font-normal">(Opsional)</span></label>
                                <div class="mt-2">
                                    <input type="tel" name="phone" id="phone" value="{{ old('phone') }}" autocomplete="tel" placeholder="0812..." class="block w-full rounded-lg border-0 py-2.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-slate-900 sm:text-sm sm:leading-6">
                                </div>
                                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Produk & alasan --}}
                <div class="overflow-hidden rounded-xl bg-white shadow-sm border border-slate-200">
                    <div class="border-b border-slate-200 bg-slate-50/50 px-6 py-4">
                        <h2 class="font-semibold text-slate-900">2. Produk & Alasan Retur</h2>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <label for="product_id" class="block text-sm font-medium leading-6 text-slate-900">Pilih Produk *</label>
                                <div class="mt-2">
                                    <select id="product_id" name="product_id" required class="block w-full rounded-lg border-0 py-2.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-slate-900 sm:text-sm sm:leading-6 bg-white">
                                        <option value="">Pilih produk...</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}" @selected(old('product_id') == $product->id)>{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <x-input-error :messages="$errors->get('product_id')" class="mt-2" />
                            </div>
                            <div>
                                <label for="invoice_number" class="block text-sm font-medium leading-6 text-slate-900">Nomor Pesanan/Invoice <span class="text-slate-400 font-normal">(Opsional)</span></label>
                                <div class="mt-2">
                                    <input type="text" name="invoice_number" id="invoice_number" value="{{ old('invoice_number') }}" placeholder="Contoh: INV-10021" class="block w-full rounded-lg border-0 py-2.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-slate-900 sm:text-sm sm:leading-6 uppercase">
                                </div>
                                <x-input-error :messages="$errors->get('invoice_number')" class="mt-2" />
                            </div>
                        </div>

                        <div>
                            <label for="reason" class="block text-sm font-medium leading-6 text-slate-900">Deskripsi Alasan Retur *</label>
                            <div class="mt-2">
                                <textarea id="reason" name="reason" rows="4" required placeholder="Jelaskan secara singkat masalah pada produk..." class="block w-full rounded-lg border-0 py-2.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-slate-900 sm:text-sm sm:leading-6">{{ old('reason') }}</textarea>
                            </div>
                            <x-input-error :messages="$errors->get('reason')" class="mt-2" />
                        </div>

                        <div>
                            <label for="refund_method" class="block text-sm font-medium leading-6 text-slate-900">Metode Pengembalian Dana <span class="text-slate-400 font-normal">(Opsional)</span></label>
                            <div class="mt-2">
                                <select id="refund_method" name="refund_method" class="block w-full rounded-lg border-0 py-2.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-slate-900 sm:text-sm sm:leading-6 bg-white">
                                    <option value="">Akan ditentukan nanti</option>
                                    @foreach (['Transfer Bank', 'E-Wallet', 'Store Credit'] as $method)
                                        <option value="{{ $method }}" @selected(old('refund_method') === $method)>{{ $method }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <x-input-error :messages="$errors->get('refund_method')" class="mt-2" />
                        </div>
                    </div>
                </div>

                {{-- Bukti pendukung --}}
                <div class="overflow-hidden rounded-xl bg-white shadow-sm border border-slate-200">
                    <div class="border-b border-slate-200 bg-slate-50/50 px-6 py-4 flex items-center justify-between">
                        <h2 class="font-semibold text-slate-900">3. Bukti Foto / Video</h2>
                        <span class="inline-flex items-center gap-x-1.5 rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10">
                            <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                            Diperiksa oleh AI
                        </span>
                    </div>
                    <div class="p-6">
                        <p class="text-sm text-slate-600 mb-4">Unggah foto jelas yang menunjukkan kerusakan atau kondisi barang yang tidak sesuai. AI kami akan memverifikasi foto Anda untuk mempercepat proses pengembalian dana.</p>
                        
                        <label for="evidences" class="relative block w-full rounded-xl border-2 border-dashed border-slate-300 p-12 text-center hover:border-slate-400 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-900 focus:ring-offset-2 transition-colors cursor-pointer group">
                            <svg class="mx-auto h-12 w-12 text-slate-300 group-hover:text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline>
                            </svg>
                            <span class="mt-4 block text-sm font-semibold text-slate-900">Pilih foto atau tarik ke sini</span>
                            <span class="mt-1 block text-xs text-slate-500">PNG, JPG, MP4 hingga 5MB</span>
                            <input id="evidences" name="evidences[]" type="file" multiple accept="image/*,video/*" class="sr-only">
                        </label>
                        
                        <x-input-error :messages="$errors->get('evidences')" class="mt-2" />
                        @for ($i = 0; $i < 5; $i++)
                            <x-input-error :messages="$errors->get(\"evidences.$i\")" class="mt-2" />
                        @endfor
                    </div>
                </div>

                {{-- Submit --}}
                <div class="flex items-center justify-end gap-4 pt-4 border-t border-slate-200">
                    <a href="{{ route('portal.home') }}" class="text-sm font-semibold leading-6 text-slate-900 hover:text-slate-700">Batalkan</a>
                    <button type="submit" class="rounded-lg bg-slate-900 px-8 py-3 text-sm font-semibold text-white shadow-sm hover:bg-slate-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-slate-900 transition-colors">
                        Ajukan Retur Sekarang
                    </button>
                </div>
            </form>
        </main>

        @include('portal.partials.footer')
    </div>
</x-guest-layout>