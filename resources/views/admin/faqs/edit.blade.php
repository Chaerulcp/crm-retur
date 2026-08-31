<x-form-page title="Ubah FAQ"
             :subtitle="$faq->question"
             :action="route('admin.faqs.update', $faq)"
             method="PUT"
             submit-label="Simpan Perubahan"
             :cancel-href="route('admin.faqs.index')">
    @include('admin.faqs._form')
</x-form-page>