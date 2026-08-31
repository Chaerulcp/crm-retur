<?php

namespace App\Http\Requests\Staff;

use App\Enums\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProcessRefundRequest extends FormRequest
{
    /**
     * Metode refund yang didukung aplikasi.
     *
     * @var array<int, string>
     */
    public const REFUND_METHODS = ['Transfer Bank', 'E-Wallet', 'Tunai'];

    public function authorize(): bool
    {
        // Dilindungi ganda: middleware route 'role:Manajemen,Admin' dan authorize().
        return $this->user()?->hasRole(Role::Manajemen, Role::Admin) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'refund_method' => ['required', Rule::in(self::REFUND_METHODS)],
            'refund_proof' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:4096'],
            'note' => ['nullable', 'string', 'max:1000'],
        ];
    }
}