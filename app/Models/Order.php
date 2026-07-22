<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    protected $fillable = [
        'invoice_no',
        'user_id',
        'total_amount',
        'discount_amount',
        'paid_amount',
        'change_amount',
    ];

    // Order Item Relationship
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // Order Items Relationship (Alias)
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // Cashier
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
