<x-form-page :title="'Ubah Produk: ' . $product->name"
             :subtitle="$product->sku"
             :action="route('admin.products.update', $product)"
             method="PUT"
             submit-label="Simpan Perubahan"
             :cancel-href="route('admin.products.index')">
    @include('admin.products._form')
</x-form-page>