<?php

namespace App\Repositories\Eloquent;

use App\Models\Order;
use App\Repositories\Contracts\OrderRepositoryInterface;

class OrderRepository implements OrderRepositoryInterface
{
    public function getPaginated($perPage = 15)
    {
        return Order::with('user')->latest()->paginate($perPage);
    }

    public function getOrderDetails($id)
    {
        return Order::with(['user', 'orderItems.product'])->findOrFail($id);
    }
}
