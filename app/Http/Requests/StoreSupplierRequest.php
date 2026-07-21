<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSupplierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $supplier = $this->route('supplier');

        return [
            'name' => ['required', 'string', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'phone' => [
                'required',
                'string',
                Rule::unique('suppliers', 'phone')->ignore($supplier?->id),
            ],
            'address' => ['nullable', 'string'],
            'due_amount' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
