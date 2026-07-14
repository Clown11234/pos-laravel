<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Repositories\Eloquent\OrderRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;

class OrderService
{
    protected $orderRepo;

    public function __construct(OrderRepository $orderRepo)
    {
        $this->orderRepo = $orderRepo;
    }

    // Sales History
    public function getSaleHistory()
    {
        return $this->orderRepo->getPaginated(15);
    }

    // Invoice Details
    public function getOrderDetails($id)
    {
        return $this->orderRepo->getOrderDetails($id);
    }

    // Stock နှုတ် မယ် Discount တွက်မယ
    public function processCheckout(array $items, float $paidAmount, float $discountAmount = 0)
    {
        return DB::transaction(function () use ($items, $paidAmount, $discountAmount) {
            $subtotalAmount = 0;

            foreach ($items as $item) {
                $product = Product::findOrFail($item['id']);
                if ($product->stock_quantity < $item['qty']) {
                    throw new Exception("{$product->name_en} သည် လက်ကျန်မလုံလောက်တော့ပါ။");
                }
                $subtotalAmount += $product->selling_price * $item['qty'];
            }

            // အသားတင် ကျသင့်ငွေ ကို တွက်ချက်
            // Discount က မူရင်းကျသင့်ငွေထက် မများရဘူး
            if ($discountAmount > $subtotalAmount) {
                $discountAmount = $subtotalAmount;
            }
            $netTotalAmount = $subtotalAmount - $discountAmount;

            // ပိုက်ဆံပေးတာ မလောက်ရင်
            if ($paidAmount < $netTotalAmount) {
                throw new Exception('ငွေ မလောက်ပါဘူးဗျ။');
            }

            // Order စာရင်း သိမ်း
            $order = Order::create([
                'invoice_no' => 'INV-' . date('Ymd') . '-' . strtoupper(uniqid()),
                'user_id' => Auth::id(),
                'total_amount' => $netTotalAmount,
                'discount_amount' => $discountAmount,
                'paid_amount' => $paidAmount,
                'change_amount' => $paidAmount - $netTotalAmount,
            ]);

            // ပစ္စည်းစာရင်းသွင်းပြီး stock နှုတ်
            foreach ($items as $item) {
                $product = Product::find($item['id']);
                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $item['qty'],
                    'price' => $product->selling_price,
                    'total' => $product->selling_price * $item['qty'],
                ]);

                $product->decrement('stock_quantity', $item['qty']);
            }

            return $order;
            });
        }
}
