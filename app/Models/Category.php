<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    // Soft Delete Trait ကို သုံးစွဲခြင်း
    use SoftDeletes, HasFactory;

    // Mass Assignment မှ ကာကွယ်ရန် ခွင့်ပြုထားသော Column များ စာရင်း
    protected $fillable = [
        'name_en',
        'name_mm',
        'slug',
        'is_active'
    ];

    // Cat တစ်ခုမှာ Products တွေ များကြီးရှိ
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    // Category တွေ ယူမယ်
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    // ဒါလဲတူတူဘဲ Localization
    public function getNameAttribute(): string
    {
        return app()->getLocale() === 'mm'
            ? ($this->name_mm ?? $this->name_en)
            : $this->name_en;
    }
}
