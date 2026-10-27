<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use App\Models\Order;

class SalesController extends Controller
{
    public function index()
    {
        $sellerId = auth()->id();

        $selectedStatuses = ['paid', 'processed', 'packing', 'shipped', 'delivered', 'completed'];

        $ordersQuery = Order::whereHas('items', fn ($q) => $q->where('seller_id', $sellerId));

        $totalSales = (clone $ordersQuery)->whereIn('status', $selectedStatuses)
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->where('order_items.seller_id', $sellerId)
            ->sum('order_items.subtotal');

        $totalCommissions = Commission::where('seller_id', $sellerId)->sum('commission_amount');
        $totalNet = Commission::where('seller_id', $sellerId)->sum('net_amount');

        $commissions = Commission::with(['order', 'orderItem.product'])
            ->where('seller_id', $sellerId)
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $monthly = (clone $ordersQuery)->whereIn('status', $selectedStatuses)
            ->where('orders.created_at', '>=', now()->startOfMonth())
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->where('order_items.seller_id', $sellerId)
            ->sum('order_items.subtotal');

        return view('seller.sales.index', compact(
            'totalSales', 'totalCommissions', 'totalNet', 'commissions', 'monthly'
        ));
    }
}
