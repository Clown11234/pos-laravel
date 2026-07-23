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

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public static function createSupplier(array $data): self
    {
        $data['due_amount'] = $data['due_amount'] ?? 0;
        return self::create($data);
    }

    public function updateSupplier(array $data): bool
    {
        return $this->update($data);
    }
}
