<div>
    <x-input-label for="question" value="Pertanyaan *" />
    <x-text-input id="question" name="question" type="text" class="mt-1.5 block w-full"
        :value="old('question', $faq->question ?? '')" required autofocus />
    <x-input-error :messages="$errors->get('question')" class="mt-2" />
</div>

<div>
    <x-input-label for="answer" value="Jawaban *" />
    <textarea id="answer" name="answer" rows="5" required
        placeholder="Tuliskan jawaban lengkap"
        class="input mt-1.5 block w-full">{{ old('answer', $faq->answer ?? '') }}</textarea>
    <x-input-error :messages="$errors->get('answer')" class="mt-2" />
</div>

<div>
    <x-input-label for="category" value="Kategori (opsional)" />
    <x-text-input id="category" name="category" type="text" class="mt-1.5 block w-full"
        :value="old('category', $faq->category ?? '')" placeholder="Umum, Pengiriman, Refund, Produk" />
    <x-input-error :messages="$errors->get('category')" class="mt-2" />
</div>