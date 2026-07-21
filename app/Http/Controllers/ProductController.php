<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Services\ProductService;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $productService;

    // Service Injection
    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'category_id']);

        $products = $this->productService->getFilteredProducts($filters, 15);
        $categories = Category::all();

        return view('products.index', compact('products', 'categories'));
    }

    public function store(StoreProductRequest $request)
    {
        $product = $this->productService->createProduct($request->validated());

        return response()->json([
            'success' => true,
            'message' => __('messages.product_created_success'), // HardCode မသုံးရပါ
            'data'    => $product
        ], 201);
    }

    public function edit($id)
    {
        $product = $this->productService->getProductById((int)$id);
        return response()->json($product);
    }

    public function update(UpdateProductRequest $request, $id)
    {
        $product = $this->productService->updateProduct((int)$id, $request->validated());

        return response()->json([
            'success' => true,
            'message' => __('messages.product_updated_success'),
            'data'    => $product
        ]);
    }

    public function destroy($id)
    {
        $this->productService->deleteProduct((int)$id);
        return redirect()->back()->with('success', __('messages.product_deleted_success'));
    }

    public function pos()
    {
        $products = $this->productService->getAllProductsForPos();
        return view('products.pos', compact('products'));
    }
}
