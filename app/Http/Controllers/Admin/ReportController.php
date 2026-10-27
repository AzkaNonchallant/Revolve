<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $activeStatuses = ['paid', 'processed', 'packing', 'shipped', 'delivered', 'completed'];
        $period = $request->get('period', 'daily');

        $dateFilter = match ($period) {
            'weekly' => now()->subWeeks(4),
            'monthly' => now()->subMonths(6),
            'yearly' => now()->subYears(1),
            default => now()->subDays(30),
        };

        $ordersQuery = Order::whereIn('status', $activeStatuses)
            ->where('created_at', '>=', $dateFilter);

        $totalOmzet = (clone $ordersQuery)->sum('total');
        $totalTransaksi = (clone $ordersQuery)->count();
        $totalKomisi = DB::table('commissions')
            ->where('created_at', '>=', $dateFilter)
            ->sum('commission_amount');

        $topProducts = Product::with(['primaryImage'])
            ->withCount(['orderItems as sold_count' => fn ($q) => $q->whereHas('order', fn ($o) => $o->whereIn('status', $activeStatuses)->where('created_at', '>=', $dateFilter))])
            ->orderByDesc('sold_count')
            ->take(10)
            ->get();

        $topCategories = Product::withCount(['orderItems as sold_count' => fn ($q) => $q->whereHas('order', fn ($o) => $o->whereIn('status', $activeStatuses)->where('created_at', '>=', $dateFilter))])
            ->with('category')
            ->get()
            ->filter(fn ($p) => $p->category)
            ->groupBy('category.name')
            ->map(fn ($group) => $group->sum('sold_count'))
            ->sortDesc()
            ->take(10);

        $topSellers = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->select('order_items.seller_id', DB::raw('SUM(order_items.subtotal) as total_sales'))
            ->whereIn('orders.status', $activeStatuses)
            ->where('orders.created_at', '>=', $dateFilter)
            ->whereNotNull('order_items.seller_id')
            ->groupBy('order_items.seller_id')
            ->orderByDesc('total_sales')
            ->take(10)
            ->get();

        $topSellersUser = User::whereIn('id', $topSellers->pluck('seller_id'))
            ->with('shop')
            ->get()
            ->keyBy('id');

        $chartData = (clone $ordersQuery)
            ->selectRaw('DATE(created_at) as day, SUM(total) as total_sales')
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        return view('admin.reports.index', compact(
            'period', 'totalOmzet', 'totalTransaksi', 'totalKomisi',
            'topProducts', 'topCategories', 'topSellers', 'topSellersUser', 'chartData'
        ));
    }
}
