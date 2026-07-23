<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use SoftDeletes , HasFactory;

    // Mass Assignment Security
    protected $fillable = [
        'category_id',
        'product_code',
        'name_en',
        'name_mm',
        'cost_price',
        'selling_price',
        'stock_quantity',
        'alert_quantity',
    ];

    // N+1 Problem ကို ကာကွယ်ရန်
    protected $with = ['category'];

    // Local Scope + Conditional Queries (when)
    public function scopeSearch($query, ?string $search)
    {
        return $query->when($search, function ($q) use ($search) {
            return $q->where(function ($sub) use ($search) {
                $sub->where('name_en', 'like', "%{$search}%")
                    ->orWhere('name_mm', 'like', "%{$search}%")
                    ->orWhere('product_code', 'like', "%{$search}%");
            });
        });
    }

    public function scopeOfCategory($query, ?int $categoryId)
    {
        return $query->when($categoryId, function ($q) use ($categoryId) {
            return $q->where('category_id', $categoryId);
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Accessor
    // localization အပေါ်မှုတည်ပြီး Name ယူဖို့ ( View မှာ Condition မစစ်ရအောင်လို့ )
    public function getNameAttribute(): string
    {
        return app()->getLocale() === 'mm'
            ? ($this->name_mm ?? $this->name_en)
            : $this->name_en;
    }

    // Stock Quantity နည်းရင် Text color ပြောင်း
    public function getStockClassAttribute(): string
    {
        return $this->stock_quantity <= $this->alert_quantity
            ? 'text-danger fw-bold'
            : 'text-dark fw-semibold';
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock_quantity', '<=', 'alert_quantity');
    }
}
