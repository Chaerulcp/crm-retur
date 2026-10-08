<x-form-page title="Tambah Produk"
             :action="route('admin.products.store')"
             submit-label="Simpan Produk"
             :cancel-href="route('admin.products.index')">
    @include('admin.products._form')
</x-form-page>