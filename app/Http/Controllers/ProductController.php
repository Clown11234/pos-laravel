<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Services\ProductService;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $productRepo;

    public function __construct(ProductRepositoryInterface $productRepo)
    {
        $this->productRepo = $productRepo;
    }

    /**
     * Product List View with Search & Pagination
     */
    public function index(Request $request)
    {
        // URL မှ လာသော Search နှင့် Category ID များကို ဖတ်ယူခြင်း
        $filters = [
            'search' => $request->get('search'),
            'category_id' => $request->get('category_id'),
        ];

        $products = $this->productRepo->getAllPaginated(15, $filters);

        $categories = \App\Models\Category::all();

        return view('products.index', compact('products', 'categories'));
    }

    /**
     * 📥 [မေ့ကျန်ခဲ့သော Method] Product အသစ်ကို Repository မှတစ်ဆင့် သိမ်းဆည်းရန် (AJAX)
     */
    public function store(Request $request)
    {
        // ရောင်းဈေးသည် အရင်းဈေးထက် ကြီးရမည်ဟူသော Validation အပါအဝင် စစ်ဆေးခြင်း
        $validated = $request->validate([
            'category_id'    => 'required|integer',
            'product_code'   => 'required|string|unique:products,product_code',
            'name_en'        => 'required|string|max:255',
            'name_mm'        => 'required|string|max:255',
            'cost_price'     => 'required|numeric|min:0',
            'selling_price'  => 'required|numeric|gt:cost_price',
            'stock_quantity' => 'required|integer|min:0',
            'alert_quantity' => 'required|integer|min:0',
        ]);

        // Repository Architecture အတိုင်း ဆောက်လုပ်ခြင်း
        $product = $this->productRepo->create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully!',
            'data'    => $product
        ], 201);
    }

    /**
     * ပြင်ဆင်ရန်အတွက် Single Product ဒေတာကို JSON ဖြင့် ပေးပို့ခြင်း
     */
    public function edit($id)
    {
        $product = $this->productRepo->findById($id);
        return response()->json($product);
    }

    /**
     * ပြင်ဆင်လိုက်သော ဒေတာများကို သိမ်းဆည်းရန် (AJAX)
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name_en' => 'required|string|max:255',
            'name_mm' => 'required|string|max:255',
            'product_code' => 'required|string|unique:products,product_code,' . $id,
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|gt:cost_price',
            'stock_quantity' => 'required|integer|min:0',
            'alert_quantity' => 'required|integer|min:0',
        ]);

        $product = $this->productRepo->update($id, $validated);

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully!',
            'data' => $product
        ]);
    }

    /**
     * Soft Delete လုပ်ဆောင်ရန်
     */
    public function destroy($id)
    {
        $this->productRepo->delete($id);
        return redirect()->back()->with('success', 'Product moved to trash.');
    }

    /**
     * Live POS Counter Screen
     */
    public function pos()
    {
        $products = \App\Models\Product::with('category')->get();

        return view('products.pos', compact('products'));
    }
}
