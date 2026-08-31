<x-form-page :title="'Ubah Staf: ' . $user->name"
             :subtitle="$user->email"
             :action="route('admin.users.update', $user)"
             method="PUT"
             submit-label="Simpan Perubahan"
             :cancel-href="route('admin.users.index')">
    @include('admin.users._form')
</x-form-page>