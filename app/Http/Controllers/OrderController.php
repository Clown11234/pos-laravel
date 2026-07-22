<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class OrderController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    // Invoice Details
    public function showInvoice(int $id): View
    {
        $order = $this->orderService->getOrderDetails($id);

        return view('orders.partials.invoice_modal', compact('order'));
    }

    // POS Checkout
    public function checkout(CheckoutRequest $request): JsonResponse
    {
        $order = $this->orderService->processCheckout($request->validated());

        return response()->json([
            'success'  => true,
            'message'  => __('messages.checkout_success'),
            'order_id' => $order->id
        ], 201);
    }

    // Sales History
    public function history(): View
    {
        $orders = $this->orderService->getSaleHistory();

        return view('orders.history', compact('orders'));
    }
}
