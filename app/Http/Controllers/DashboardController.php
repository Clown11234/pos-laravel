<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Stock နည်းနေတာတွေကို Model scope ကနေလှမ်းယူ
        $lowStockProducts = Product::lowStock()->get();

        // Dashboard ထဲ ပြချင်တာတွေ
        $totalOrders = Order::count();
        $totalRevenue = Order::sum('total_amount');

        // Blade ဆီ data လှမ်းပို့
        return view('dashboard', compact('lowStockProducts', 'totalOrders', 'totalRevenue'));
    }
}
