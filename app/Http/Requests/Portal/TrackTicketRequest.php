<?php

namespace App\Http\Requests\Portal;

use Illuminate\Foundation\Http\FormRequest;

class TrackTicketRequest extends FormRequest
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
        return [
            'nomor' => ['nullable', 'string', 'max:64'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nomor.string' => 'Nomor tiket tidak valid.',
            'nomor.max' => 'Nomor tiket terlalu panjang.',
        ];
    }
}
