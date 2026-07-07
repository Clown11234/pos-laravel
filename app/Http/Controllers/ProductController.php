<?php

namespace App\Http\Controllers;

use App\Services\ProductService;
use App\Repositories\Contracts\ProductRepositoryInterface; // Repository တိုက်ရိုက်သုံးရန် (သို့မဟုတ် Service မှတစ်ဆင့်သွားနိုင်သည်)
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $productRepo;

    public function __construct(ProductRepositoryInterface $productRepo)
    {
        $this->productRepo = $productRepo;
    }

    public function index(Request $request)
    {
        // URL မှ လာသော Search နှင့် Category ID များကို ဖတ်ယူခြင်း
        $filters = [
            'search' => $request->get('search'),
            'category_id' => $request->get('category_id'),
        ];

        $products = $this->productRepo->getAllPaginated(10, $filters);

        return view('products.index', compact('products'));
    }

    // Update လုပ်
    public function edit($id)
    {
        $product = $this->productRepo->findById($id);
        return response()->json($product);
    }

    public function update(Request $request, $id)
    {
        // Validation ကို Controller ထဲ၌ ရိုးရိုးပဲ စစ်လိုက်ပါမည် (ဒေတာတူစစ်ဆေးမှုများ လွယ်ကူစေရန်)
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
}
