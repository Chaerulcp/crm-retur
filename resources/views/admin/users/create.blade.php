<x-form-page title="Tambah Staf"
             :action="route('admin.users.store')"
             submit-label="Simpan Staf"
             :cancel-href="route('admin.users.index')">
    @include('admin.users._form')
</x-form-page>