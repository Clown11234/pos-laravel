<?php

namespace App\Repositories\Eloquent;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;

class ProductRepository implements ProductRepositoryInterface
{
    public function getAllPaginated(int $perPage = 10, array $filters = [])
    {
        return Product::with('category')
            ->search($filters['search'] ?? null)
            ->ofCategory($filters['category_id'] ?? null)
            ->latest()
            ->paginate($perPage);
    }

    public function findById(int $id)
    {
        return Product::findOrFail($id);
    }

    public function create(array $data)
    {
        return Product::create($data);
    }

    public function update(int $id, array $data)
    {
        $product = $this->findById($id);
        $product->update($data);
        return $product;
    }

    public function delete(int $id)
    {
        $product = $this->findById($id);
        return $product->delete(); // Soft Delete အလုပ်လုပ်မည်
    }

    public function getTrashed()
    {
        return Product::onlyTrashed()->with('category')->get();
    }
    public function restore(int $id)
    {
        $product = Product::onlyTrashed()->findOrFail($id);
        return $product->restore();
    }
}
