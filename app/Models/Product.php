<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'category_id',
        'name_en',
        'name_mm',
        'product_code',
        'description',
        'cost_price',
        'selling_price',
        'stock_quantity',
        'alert_quantity',
        'image'
    ];

    public function scopeSearch(Builder $query, ?string $keyword): Builder
    {
        if (!$keyword) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($keyword) {
            $q->where('name_en', 'LIKE', "%{$keyword}%")
                ->orWhere('name_mm', 'LIKE', "%{$keyword}%")
                ->orWhere('product_code', 'LIKE', "%{$keyword}%");
        });
    }

    public function scopeOfCategory(Builder $query, ?int $categoryId): Builder
    {
        if (!$categoryId) {
            return $query;
        }
        return $query->where('category_id', $categoryId);
    }

    // Product တစ်ခုမှ Category တစ်ခုရှိ
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function getDisplayNameAttribute(): string
    {
        return app()->getLocale() === 'mm' ? $this->name_mm : $this->name_en;
    }


    public function scopeActive(Builder $query)
    {
        return $query;
    }
}
