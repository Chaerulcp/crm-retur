<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="eyebrow">Administrasi</p>
                <h1 class="mt-1 font-display text-2xl font-bold text-ink">Manajemen Produk</h1>
            </div>
            <a href="{{ route('admin.products.create') }}" class="btn-primary">
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z"/></svg>
                Tambah Produk
            </a>
        </div>
    </x-slot>

    @if (session('success'))
        <div class="flash-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="flash-error">{{ session('error') }}</div>
    @endif

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="th">Nama</th>
                        <th class="th">SKU</th>
                        <th class="th">Deskripsi</th>
                        <th class="th">Status</th>
                        <th class="th text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($products as $product)
                        <tr class="transition hover:bg-brand-50/40">
                            <td class="td font-semibold text-slate-800">{{ $product->name }}</td>
                            <td class="td font-mono text-xs text-slate-600">{{ $product->sku }}</td>
                            <td class="td max-w-xs text-slate-500">{{ str($product->description)->limit(60, '…')->toString() ?: '-' }}</td>
                            <td class="td">
                                @if ($product->is_active)
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-200">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-red-50 px-2.5 py-1 text-xs font-semibold text-red-700 ring-1 ring-inset ring-red-200">
                                        <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span> Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="td text-right">
                                <a href="{{ route('admin.products.edit', $product) }}"
                                   class="text-xs font-semibold text-brand-600 hover:text-brand-700 hover:underline">Ubah</a>

                                <form method="POST" action="{{ route('admin.products.destroy', $product) }}"
                                      class="inline" onsubmit="return confirm('Hapus produk {{ $product->name }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="ml-3 text-xs font-semibold text-red-600 hover:text-red-700 hover:underline">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="td px-6 py-14 text-center text-slate-400">Belum ada produk terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($products->hasPages())
            <div class="border-t border-slate-100 bg-slate-50/60 px-5 py-3.5">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</x-app-layout>