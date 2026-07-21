<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;

class OrderService
{
   // Sales History
    public function getSaleHistory(int $perPage = 15): LengthAwarePaginator
    {
        return Order::with('user')
            ->when(request()->filled('search'), function ($query) {
                $searchId = str_replace('#INV-', '', request('search'));
                $query->where('id', 'like', "%{$searchId}%");
            })
            ->latest()
            ->paginate($perPage);
    }

    // Invoice Details
    public function getOrderDetails(int $id): Order
    {
        return Order::with(['user', 'orderItems.product'])->findOrFail($id);
    }

    // POS Checkout
    public function processCheckout(array $data): Order
    {
        $items = $data['items'];
        $paidAmount = (float) $data['paid_amount'];
        $discountAmount = (float) ($data['discount_amount'] ?? 0);

        return DB::transaction(function () use ($items, $paidAmount, $discountAmount) {
            $subtotalAmount = 0;
            $productsToUpdate = [];

            // Stock စစ်ဆေးခြင်းနှင့် Subtotal တွက်ချက်ခြင်း
            foreach ($items as $item) {
                $product = Product::findOrFail($item['id']);

                if ($product->stock_quantity < $item['qty']) {
                    throw new Exception("{$product->name} သည် လက်ကျန်မလုံလောက်တော့ပါ။");
                }

                $subtotalAmount += $product->selling_price * $item['qty'];
                $productsToUpdate[] = [
                    'model' => $product,
                    'qty'   => $item['qty']
                ];
            }

            // Discount
            if ($discountAmount > $subtotalAmount) {
                $discountAmount = $subtotalAmount;
            }
            $netTotalAmount = $subtotalAmount - $discountAmount;

            // ပေးငွေမလောက်
            if ($paidAmount < $netTotalAmount) {
                throw new Exception('ပေးသွင်းငွေ မလုံလောက်ပါခင်ဗျာ။');
            }

            // Order သိမ်းဆည်းခြင်း
            $order = Order::create([
                'invoice_no'      => 'INV-' . date('Ymd') . '-' . strtoupper(uniqid()),
                'user_id'         => Auth::id(),
                'total_amount'    => $netTotalAmount,
                'discount_amount' => $discountAmount,
                'paid_amount'     => $paidAmount,
                'change_amount'   => $paidAmount - $netTotalAmount,
            ]);

            // Order Items ထည့်သွင်းခြင်းနှင့် Stock နှုတ်ခြင်း
            foreach ($productsToUpdate as $data) {
                /** @var Product $product */
                $product = $data['model'];
                $qty = $data['qty'];

                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity'   => $qty,
                    'price'      => $product->selling_price,
                    'total'      => $product->selling_price * $qty,
                ]);

                $product->decrement('stock_quantity', $qty);
            }

            return $order;
        });
    }
}
