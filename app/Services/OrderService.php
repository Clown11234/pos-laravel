<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;

class OrderService
{
    // Stock နှုတ်
    public function processCheckout(array $items, float $paidAmount)
    {
        return DB::transaction(function () use ($items, $paidAmount) {
            $totalAmount = 0;

            // ရှိမရှိစစ် ပြီးရင်တွက်
            foreach ($items as $item) {
                $product = Product::findOrFail($item['id']);
                if ($product->stock_quantity < $item['qty']) {
                    throw new Exception("{$product->name_en} သည် လက်ကျန်မလုံလောက်တော့ပါ။");
                }
                $totalAmount += $product->selling_price * $item['qty'];
            }

            if ($paidAmount < $totalAmount) {
                throw new Exception('ပေးငွေ မလုံလောက်ပါ။');
            }

            // Order သိမ်း
            $order = Order::create([
                'invoice_no'    => 'INV-' . date('Ymd') . '-' . strtoupper(uniqid()),
                'user_id'       => Auth::id(),
                'total_amount'  => $totalAmount,
                'paid_amount'   => $paidAmount,
                'change_amount' => $paidAmount - $totalAmount,
            ]);

            // ပစ္စည်းစာရင်းသွင်းပြီး စတော့နုတ်ခြင်း
            foreach ($items as $item) {
                $product = Product::find($item['id']);
                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity'   => $item['qty'],
                    'price'      => $product->selling_price,
                    'total'      => $product->selling_price * $item['qty'],
                ]);

                $product->decrement('stock_quantity', $item['qty']);
            }

            return $order;
        });
    }
}
