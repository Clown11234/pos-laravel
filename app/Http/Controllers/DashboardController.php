<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::now('Asia/Yangon')->startOfDay();
        $startOfMonth = Carbon::now('Asia/Yangon')->startOfMonth();

        // Stock နည်းနေတာတွေကို Model scope ကနေလှမ်းယူ
        $lowStockProducts = Product::lowStock()->get();

        //  Dashboard ထဲ ပြချင်တာတွေ (All-Time)
        $totalOrders = Order::count();
        $totalRevenue = Order::sum('total_amount');

        // ဒီနေ့ရောင်းရငွေ စုစုပေါင်း (Today's Total Sales)
        $todaySales = Order::where('created_at', '>=', $today)->sum('total_amount');

        // ဒီလရောင်းရငွေ စုစုပေါင်း (This Month's Total Sales)
        $monthSales = Order::where('created_at', '>=', $startOfMonth)->sum('total_amount');

        // ဒီနေ့ ရောင်းခဲ့ရသော ပြေစာ အရေအတွက် (Today's Total Orders)
        $todayOrdersCount = Order::where('created_at', '>=', $today)->count();

        // အရောင်းရဆုံး ပစ္စည်း ၅ ခု စာရင်း (Top 5 Best Selling Products)
        $topProducts = OrderItem::select('product_id', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(quantity * price) as total_revenue'))
            ->with('product')
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();

        // Blade ဆီသို့ ဒေတာအားလုံးကို စုစည်းပြီး လှမ်းပို့ခြင်း
        return view('dashboard', compact(
            'lowStockProducts',
            'totalOrders',
            'totalRevenue',
            'todaySales',
            'monthSales',
            'todayOrdersCount',
            'topProducts'
        ));
    }
}
