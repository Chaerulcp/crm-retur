<?php

namespace App\Http\Requests\Portal;

use App\Rules\RecaptchaVerified;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;

class StoreReturnTicketRequest extends FormRequest
{
    /**
     * Portal bersifat publik (tanpa login), jadi selalu diizinkan.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $rules = [
            'customer_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100'],
            'phone' => ['nullable', 'string', 'max:20'],
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'invoice_number' => ['nullable', 'string', 'max:50'],
            'reason' => ['required', 'string', 'max:2000'],
            'refund_method' => ['nullable', Rule::in(['Transfer Bank', 'E-Wallet', 'Store Credit'])],
            'evidences' => ['nullable', 'array', 'max:5'],
            'evidences.*' => ['required', 'file'],
        ];

        // CAPTCHA hanya aktif bila secret reCAPTCHA dikonfigurasi via env.
        if (filled(config('services.recaptcha.secret'))) {
            $rules['g-recaptcha-response'] = ['required', new RecaptchaVerified];
        }

        return $rules;
    }

    /**
     * Validasi berkas bukti: gambar (image, maks 5120 KB) ATAU video (video, maks 51200 KB).
     * Kedua aturan tidak dapat digabung dengan OR pada validator bawaan,
     * sehingga diperiksa per berkas pada hook after().
     */
    protected function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            foreach ($this->file('evidences', []) as $index => $file) {
                if (! $file instanceof UploadedFile || ! $file->isValid()) {
                    continue;
                }

                $mime = (string) $file->getMimeType();
                $sizeKb = $file->getSize() / 1024;

                if (str_starts_with($mime, 'image/')) {
                    if ($sizeKb > 5120) {
                        $validator->errors()->add("evidences.{$index}", 'Ukuran gambar maksimal 5 MB.');
                    }
                } elseif (str_starts_with($mime, 'video/')) {
                    if ($sizeKb > 51200) {
                        $validator->errors()->add("evidences.{$index}", 'Ukuran video maksimal 50 MB.');
                    }
                } else {
                    $validator->errors()->add("evidences.{$index}", 'Berkas bukti harus berupa gambar atau video.');
                }
            }
        });
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'customer_name.required' => 'Nama wajib diisi.',
            'customer_name.max' => 'Nama maksimal 100 karakter.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'phone.max' => 'Nomor telepon maksimal 20 karakter.',
            'product_id.required' => 'Produk wajib dipilih.',
            'product_id.exists' => 'Produk yang dipilih tidak ditemukan.',
            'invoice_number.max' => 'Nomor invoice maksimal 50 karakter.',
            'reason.required' => 'Alasan retur wajib diisi.',
            'reason.max' => 'Alasan retur maksimal 2000 karakter.',
            'refund_method.in' => 'Metode refund yang dipilih tidak dikenal.',
            'evidences.array' => 'Berkas bukti tidak valid.',
            'evidences.max' => 'Berkas bukti maksimal 5 berkas.',
            'evidences.*.required' => 'Berkas bukti tidak valid.',
            'evidences.*.file' => 'Berkas bukti tidak valid.',
            'g-recaptcha-response.required' => 'Mohon centang verifikasi CAPTCHA terlebih dahulu.',
        ];
    }
}
