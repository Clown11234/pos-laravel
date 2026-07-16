<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
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

    // STORE PRODUCT
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

    public function addStock(Request $request, $id)
    {
        $request->validate([
            'added_quantity' => 'required|integer|min:1'
        ]);

        try{
            $product = Product::findOrFail($id);

            $product->increment('stock_quantity', $request->added_quantity);

            return response()->json([
                'success' => true,
                'message' => 'Product အသစ်ထပ်ထည့် ပြီးပါပြီ။',
                'new_stock' => $product->stock_quantity
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'တစ်ခုခုတော့မှားနေပြီ။'
            ], 500);
        }
    }

    // EDIT
    public function edit($id)
    {
        $product = $this->productRepo->findById($id);
        return response()->json($product);
    }

    // UPDATE ( AFTER EDIT )
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

    // DELETE
    public function destroy($id)
    {
        $this->productRepo->delete($id);
        return redirect()->back()->with('success', 'Product moved to trash.');
    }

    // POS
    public function pos()
    {
        $products = \App\Models\Product::with('category')->get();

        return view('products.pos', compact('products'));
    }
}
