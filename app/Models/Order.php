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
        'paid_amount',
        'change_amount',
    ];

    // Project တစ်ခုတွင် Items အများကြီးရှိနိုင်
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // ဘယ် Cashier ရောင်းခဲ့လဲ
    public function Cashier()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Get users
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Get the items for order
    public function orderItems() : HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

}
