<div>
    <x-input-label for="name" value="Nama Produk *" />
    <x-text-input id="name" name="name" type="text" class="mt-1.5 block w-full"
        :value="old('name', $product->name ?? '')" required autofocus />
    <x-input-error :messages="$errors->get('name')" class="mt-2" />
</div>

<div>
    <x-input-label for="sku" value="SKU *" />
    <x-text-input id="sku" name="sku" type="text" class="mt-1.5 block w-full font-mono uppercase placeholder:font-sans placeholder:normal-case"
        :value="old('sku', $product->sku ?? '')" placeholder="SKU-10021" required />
    <x-input-error :messages="$errors->get('sku')" class="mt-2" />
</div>

<div>
    <x-input-label for="description" value="Deskripsi (opsional)" />
    <textarea id="description" name="description" rows="4"
        placeholder="Deskripsi singkat produk"
        class="input mt-1.5 block w-full">{{ old('description', $product->description ?? '') }}</textarea>
    <x-input-error :messages="$errors->get('description')" class="mt-2" />
</div>

<div>
    <label class="inline-flex items-center gap-2.5">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" value="1"
            @checked(old('is_active', $product->exists ? $product->is_active : true))
            class="rounded border-slate-300 text-brand-600 shadow-sm focus:ring-brand-500">
        <span class="text-sm text-slate-700">Produk aktif (bisa dipilih pada formulir retur)</span>
    </label>
    <x-input-error :messages="$errors->get('is_active')" class="mt-2" />
</div>