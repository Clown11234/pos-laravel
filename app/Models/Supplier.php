<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'contact_person',
        'phone',
        'address',
        'due_amount',
    ];

    // Supplier တစ်ယောက်မှာ Products တွေ များကြီးရှိနိုင်
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
