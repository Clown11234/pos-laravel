<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function getDashboardData(): array
    {
        $timezone = 'Asia/Yangon';
        $today = Carbon::now($timezone)->startOfDay();
        $startOfMonth = Carbon::now($timezone)->startOfMonth();

        // Low stock products from model scope
        $lowStockProducts = Product::lowStock()->get();

        // All-Time Summary Status
        $totalOrders = Order::count();
        $totalRevenue = Order::sum('total_amount');

        // Period Sales Status
        $todaySales = Order::where('created_at', '>=', $today)->sum('total_amount');
        $monthSales = Order::where('created_at', '>=', $startOfMonth)->sum('total_amount');
        $todayOrdersCount = Order::where('created_at', '>=', $today)->count();
        $totalDiscountGiven = Order::where('created_at', '>=', $startOfMonth)->sum('discount_amount');

        // Top 5 Best Selling Product
        $topProducts = OrderItem::select(
            'product_id',
            DB::raw('SUM(quantity) as total_qty'),
            DB::raw('SUM(quantity * price) as total_revenue')
        )
            ->with('product')
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();

        return compact(
            'lowStockProducts',
            'totalOrders',
            'totalRevenue',
            'todaySales',
            'monthSales',
            'todayOrdersCount',
            'topProducts',
            'totalDiscountGiven'
        );
    }
}
