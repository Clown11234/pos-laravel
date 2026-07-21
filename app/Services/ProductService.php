<?php

namespace App\Services;

use App\Models\Product;

class ProductService
{
    public function getFilteredProducts(array $filters, int $perPage = 15)
    {
        return Product::search($filters['search'] ?? null)
            ->ofCategory($filters['category_id'] ?? null)
            ->latest()
            ->paginate($perPage);
    }

    public function getAllProductsForPos()
    {
        return Product::latest()->get();
    }

    public function createProduct(array $data)
    {
        $data['product_code'] = strtoupper($data['product_code']);
        return Product::create($data);
    }

    public function getProductById(int $id)
    {
        return Product::findOrFail($id);
    }

    public function updateProduct(int $id, array $data)
    {
        $product = $this->getProductById($id);
        $data['product_code'] = strtoupper($data['product_code']);
        $product->update($data);
        return $product;
    }


    public function deleteProduct(int $id)
    {
        $product = $this->getProductById($id);
        return $product->delete();
    }
}
