<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Services\OrderService; // Service လွှဲသုံးခြင်း
use Exception;

class OrderController extends Controller
{
    protected $orderService;

    // Dependency Injection ဖြင့် Service ကို ခေါ်ယူခြင်း
    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    // Request လုပ်ရင် Service ထဲမှာတွက်
    public function checkout(Request $request)
    {
        $request->validate([
            'items'         => 'required|array|min:1',
            'paid_amount'   => 'required|numeric|min:0',
        ]);

        try {
            // Service ထဲမှာတွက်
            $order = $this->orderService->processCheckout(
                $request->input('items'),
                $request->input('paid_amount')
            );

            return response()->json([
                'success' => true,
                'message' => 'ငွေရှင်းခြင်း အောင်မြင်ပြီးပါပြီ။',
                'order_id' => $order->id
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    // History
     public function history()
     {
         $orders = $this->orderService->getSaleHistory();
         return view('orders.history', compact('orders'));
     }
}
