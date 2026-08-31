<x-form-page title="Tambah FAQ"
             :action="route('admin.faqs.store')"
             submit-label="Simpan FAQ"
             :cancel-href="route('admin.faqs.index')">
    @include('admin.faqs._form')
</x-form-page>