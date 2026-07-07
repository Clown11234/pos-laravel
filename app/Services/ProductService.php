<?php

namespace App\Services;

use App\Repositories\Contracts\ProductRepositoryInterface;

class ProductService
{
    protected $productRepo;

    // Dependency Injection ဖြင့် Interface ကို လှမ်းယူခြင်း
    public function __construct(ProductRepositoryInterface $productRepo)
    {
        $this->productRepo = $productRepo;
    }

    public function getProductList(int $perPage = 10)
    {
        return $this->productRepo->getAllPaginated($perPage);
    }

    public function createProduct(array $data)
    {
        // Business Logic Example: Product Code ကို အလိုအလျောက် စနစ်တကျ ပြုပြင်ဖန်တီးခြင်းမျိုး လုပ်နိုင်သည်
        $data['product_code'] = strtoupper($data['product_code']);

        return $this->productRepo->create($data);
    }
}
