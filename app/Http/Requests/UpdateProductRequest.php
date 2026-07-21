<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $productId = $this->route('product');

        return [
            'category_id'    => 'required|integer|exists:categories,id',
            'name_en'        => 'required|string|max:255',
            'name_mm'        => 'required|string|max:255',
            'product_code'   => 'required|string|unique:products,product_code,' . $productId,
            'cost_price'     => 'required|numeric|min:0',
            'selling_price'  => 'required|numeric|gt:cost_price',
            'stock_quantity' => 'required|integer|min:0',
            'alert_quantity' => 'required|integer|min:0',
        ];
    }
}
