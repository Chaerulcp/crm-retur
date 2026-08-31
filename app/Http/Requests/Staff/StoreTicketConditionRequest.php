<?php

namespace App\Http\Requests\Staff;

use App\Enums\ItemCondition;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTicketConditionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'condition' => ['required', Rule::in([ItemCondition::Layak->value, ItemCondition::TidakLayak->value])],
            'note' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Kondisi barang yang divonis.
     */
    public function itemCondition(): ItemCondition
    {
        return ItemCondition::from($this->validated('condition'));
    }
}