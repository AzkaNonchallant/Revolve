<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use App\Models\Order;
use App\Models\Product;

class DashboardController extends Controller
{
    public function index()
    {
        $sellerId = auth()->id();

        $selectedStatuses = ['paid', 'processed', 'packing', 'shipped', 'delivered', 'completed'];

        $totalProducts = Product::where('seller_id', $sellerId)->count();
        $activeProducts = Product::where('seller_id', $sellerId)->where('status', 1)->where('approval_status', 'approved')->count();
        $pendingProducts = Product::where('seller_id', $sellerId)->where('approval_status', 'pending')->count();

        $ordersQuery = Order::whereHas('items', fn ($q) => $q->where('seller_id', $sellerId));

        $totalOrders = (clone $ordersQuery)->whereIn('status', $selectedStatuses)->count();
        $newOrders = (clone $ordersQuery)->whereIn('status', ['paid'])->count();
        $processingOrders = (clone $ordersQuery)->whereIn('status', ['processed', 'packing'])->count();
        $completedOrders = (clone $ordersQuery)->where('status', 'completed')->count();

        $totalSales = (clone $ordersQuery)->whereIn('status', $selectedStatuses)
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->where('order_items.seller_id', $sellerId)
            ->sum('order_items.subtotal');

        $commission = Commission::where('seller_id', $sellerId)->sum('commission_amount');
        $revenue = Commission::where('seller_id', $sellerId)->sum('net_amount');

        $bestSellers = Product::with(['primaryImage'])
            ->where('seller_id', $sellerId)
            ->withCount(['orderItems as sold_count' => fn ($q) => $q->whereHas('order', fn ($o) => $o->whereIn('status', $selectedStatuses))])
            ->orderByDesc('sold_count')
            ->take(5)
            ->get();

        return view('seller.dashboard', compact(
            'totalProducts', 'activeProducts', 'pendingProducts',
            'totalOrders', 'newOrders', 'processingOrders', 'completedOrders',
            'totalSales', 'commission', 'revenue', 'bestSellers'
        ));
    }
}
