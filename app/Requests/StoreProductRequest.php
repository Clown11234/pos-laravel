<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    // စစ်ဆေးမှု မလုပ်ခင် အသုံးပြုသူတွင် အခွင့်အာဏာ ရှိမရှိ စစ်ဆေးခြင်း (လောလောဆယ် true ပေးထားမည်)
    public function authorize(): bool
    {
        return true;
    }

    // တကယ့် Validation စည်းကမ်းချက်များ
    public function rules(): array
    {
        return [
            'category_id' => 'required|exists:categories,id',
            'name_en' => 'required|string|max:255',
            'name_mm' => 'required|string|max:255',
            'product_code' => 'required|string|unique:products,product_code',
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|gt:cost_price', // ရောင်းစျေးသည် ရင်းစျေးထက် ကြီးရမည်
            'stock_quantity' => 'required|integer|min:0',
            'alert_quantity' => 'required|integer|min:0',
        ];
    }
}
