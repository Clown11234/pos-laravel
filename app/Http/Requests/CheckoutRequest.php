<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'items'             => 'required|array|min:1',
            'items.*.id'        => 'required|integer|exists:products,id',
            'items.*.qty'       => 'required|integer|min:1',
            'paid_amount'       => 'required|numeric|min:0',
            'discount_amount'   => 'nullable|numeric|min:0',
        ];
    }
}
