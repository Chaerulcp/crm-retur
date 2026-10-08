<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreFaqRequest;
use App\Http\Requests\Admin\UpdateFaqRequest;
use App\Models\Faq;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FaqController extends Controller
{
    /**
     * Tabel daftar FAQ.
     */
    public function index(): View
    {
        $faqs = Faq::query()->orderBy('category')->orderBy('question')->paginate(15);

        return view('admin.faqs.index', ['faqs' => $faqs]);
    }

    /**
     * Formulir tambah FAQ.
     */
    public function create(): View
    {
        return view('admin.faqs.create', ['faq' => new Faq]);
    }

    /**
     * Simpan FAQ baru.
     */
    public function store(StoreFaqRequest $request): RedirectResponse
    {
        Faq::create($request->validated());

        return redirect()
            ->route('admin.faqs.index')
            ->with('success', 'FAQ berhasil ditambahkan.');
    }

    /**
     * Formulir ubah FAQ.
     */
    public function edit(Faq $faq): View
    {
        return view('admin.faqs.edit', ['faq' => $faq]);
    }

    /**
     * Perbarui FAQ.
     */
    public function update(UpdateFaqRequest $request, Faq $faq): RedirectResponse
    {
        $faq->update($request->validated());

        return redirect()
            ->route('admin.faqs.index')
            ->with('success', 'FAQ berhasil diperbarui.');
    }

    /**
     * Hapus FAQ.
     */
    public function destroy(Faq $faq): RedirectResponse
    {
        $faq->delete();

        return redirect()
            ->route('admin.faqs.index')
            ->with('success', 'FAQ berhasil dihapus.');
    }
}