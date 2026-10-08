<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Tabel daftar produk.
     */
    public function index(): View
    {
        $products = Product::query()->orderBy('name')->paginate(15);

        return view('admin.products.index', ['products' => $products]);
    }

    /**
     * Formulir tambah produk.
     */
    public function create(): View
    {
        return view('admin.products.create', ['product' => new Product]);
    }

    /**
     * Simpan produk baru.
     */
    public function store(StoreProductRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        Product::create($data);

        return redirect()
            ->route('admin.products.index')
            ->with('success', "Produk {$data['name']} berhasil ditambahkan.");
    }

    /**
     * Formulir ubah produk.
     */
    public function edit(Product $product): View
    {
        return view('admin.products.edit', ['product' => $product]);
    }

    /**
     * Perbarui produk, termasuk toggle status aktif.
     */
    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        $product->update($data);

        return redirect()
            ->route('admin.products.index')
            ->with('success', "Produk {$product->name} berhasil diperbarui.");
    }

    /**
     * Hapus produk. Dicegah jika produk masih memiliki tiket retur (FK restrict).
     */
    public function destroy(Product $product): RedirectResponse
    {
        try {
            $product->delete();
        } catch (QueryException) {
            return back()->with('error', "Produk {$product->name} tidak dapat dihapus karena masih memiliki tiket retur.");
        }

        return redirect()
            ->route('admin.products.index')
            ->with('success', "Produk {$product->name} berhasil dihapus.");
    }
}