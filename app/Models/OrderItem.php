<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{

     //  Mass Assignment Constraint
    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price',
        'total'
    ];

    // Eloquent Relationships (ဒေတာဘေ့စ် အချိတ်အဆက်များ)
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    // Relationship to Product (ကုန်ပစ္စည်းနှင့် ချိတ်ဆက်ခြင်း)

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
