<?php

namespace App\Repositories\Eloquent;

use App\Models\Order;
use App\Repositories\Contracts\OrderRepositoryInterface;

class OrderRepository implements OrderRepositoryInterface
{
    public function getPaginated($perPage = 15)
    {
        $query = Order::with('user')->latest();

        // Search Request ရှိမရှိ စစ်ဆေးပြီး Invoice ID ဖြင့် ရှာဖွေခြင်း
        if (request()->has('search') && !empty(request('search'))) {
            $search = request('search');
            // #INV-5 သို့မဟုတ် 5 ဟု ရိုက်ရှာလို့ရအောင်
            $searchId = str_replace('#INV-', '', $search);

            $query->where('id', 'like', "%{$searchId}%");
        }

        return $query->paginate($perPage);
    }

    public function getOrderDetails($id)
    {
        return Order::with(['user', 'orderItems.product'])->findOrFail($id);
    }
}
