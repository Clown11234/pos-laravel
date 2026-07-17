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
        $supplierParam = $this->route('supplier');

        $supplierID = is_object($supplierParam) ? $supplierParam->id : $supplierParam;

        return [
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => [
                'required',
                'string',
                Rule::unique('suppliers', 'phone')->ignore($supplierID)
            ],
            'address' => 'nullable|string',
            'due_amount' => 'nullable|numeric|min:0'
        ];
    }
}
