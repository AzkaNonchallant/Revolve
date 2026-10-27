<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;

class StatisticsController extends Controller
{
    public function index()
    {
        $sellerId = auth()->id();

        $selectedStatuses = ['paid', 'processed', 'packing', 'shipped', 'delivered', 'completed'];

        $ordersQuery = Order::whereHas('items', fn ($q) => $q->where('seller_id', $sellerId))
            ->whereIn('status', $selectedStatuses)
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->where('order_items.seller_id', $sellerId);

        $daily = collect(range(6, 0))->mapWithKeys(function ($i) use ($ordersQuery) {
            $date = now()->subDays($i)->toDateString();

            $amount = (clone $ordersQuery)
                ->whereDate('orders.created_at', $date)
                ->sum('order_items.subtotal');

            return [$date => (int) $amount];
        });

        $weekly = collect(range(3, 0))->mapWithKeys(function ($i) use ($ordersQuery) {
            $week = now()->subWeeks($i);
            $start = $week->copy()->startOfWeek()->toDateString();
            $end = $week->copy()->endOfWeek()->toDateString();

            $amount = (clone $ordersQuery)
                ->whereBetween('orders.created_at', [$start.' 00:00:00', $end.' 23:59:59'])
                ->sum('order_items.subtotal');

            return [$start => (int) $amount];
        });

        $monthly = collect(range(5, 0))->mapWithKeys(function ($i) use ($ordersQuery) {
            $month = now()->subMonths($i);
            $amount = (clone $ordersQuery)
                ->whereBetween('orders.created_at', [$month->copy()->startOfMonth(), $month->copy()->endOfMonth()])
                ->sum('order_items.subtotal');

            return [$month->format('Y-m') => (int) $amount];
        });

        $bestSellers = Product::with(['primaryImage'])
            ->where('seller_id', $sellerId)
            ->withCount(['orderItems as sold_count' => fn ($q) => $q->whereHas('order', fn ($o) => $o->whereIn('status', $selectedStatuses))])
            ->orderByDesc('sold_count')
            ->take(10)
            ->get();

        $bestCategories = Product::where('seller_id', $sellerId)
            ->withCount(['orderItems as sold_count' => fn ($q) => $q->whereHas('order', fn ($o) => $o->whereIn('status', $selectedStatuses))])
            ->with('category')
            ->get()
            ->groupBy('category.name')
            ->map(fn ($group) => $group->sum('sold_count'))
            ->sortDesc()
            ->take(5);

        return view('seller.statistics.index', compact('daily', 'weekly', 'monthly', 'bestSellers', 'bestCategories'));
    }
}
