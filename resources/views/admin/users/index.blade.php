<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="eyebrow">Administrasi</p>
                <h1 class="mt-1 font-display text-2xl font-bold text-ink">Manajemen Staf</h1>
            </div>
            <a href="{{ route('admin.users.create') }}" class="btn-primary">
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z"/></svg>
                Tambah Staf
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
                        <th class="th">Email</th>
                        <th class="th">Peran</th>
                        <th class="th">Terdaftar</th>
                        <th class="th text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($users as $staff)
                        <tr class="transition hover:bg-brand-50/40">
                            <td class="td">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-brand-100 text-[11px] font-bold uppercase text-brand-700">
                                        {{ collect(explode(' ', $staff->name))->filter()->map(fn ($word) => mb_substr($word, 0, 1))->take(2)->implode('') }}
                                    </span>
                                    <span class="font-semibold text-slate-800">{{ $staff->name }}</span>
                                    @if ($staff->is(auth()->user()))
                                        <span class="rounded-full bg-brand-50 px-2 py-0.5 text-xs font-semibold text-brand-700 ring-1 ring-inset ring-brand-200">Anda</span>
                                    @endif
                                </div>
                            </td>
                            <td class="td text-slate-600">{{ $staff->email }}</td>
                            <td class="td">
                                <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700 ring-1 ring-inset ring-slate-200">
                                    {{ $staff->role->label() }}
                                </span>
                            </td>
                            <td class="td font-mono text-xs text-slate-500">{{ $staff->created_at->format('d M Y') }}</td>
                            <td class="td text-right">
                                <a href="{{ route('admin.users.edit', $staff) }}"
                                   class="text-xs font-semibold text-brand-600 hover:text-brand-700 hover:underline">Ubah</a>

                                @unless ($staff->is(auth()->user()))
                                    <form method="POST" action="{{ route('admin.users.destroy', $staff) }}"
                                          class="inline" onsubmit="return confirm('Hapus staf {{ $staff->name }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="ml-3 text-xs font-semibold text-red-600 hover:text-red-700 hover:underline">Hapus</button>
                                    </form>
                                @endunless
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="td px-6 py-14 text-center text-slate-400">Belum ada staf terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($users->hasPages())
            <div class="border-t border-slate-100 bg-slate-50/60 px-5 py-3.5">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</x-app-layout>