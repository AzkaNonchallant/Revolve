<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use App\Models\Order;
use App\Models\Product;
use App\Models\SellerRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $activeStatuses = ['paid', 'processed', 'packing', 'shipped', 'delivered', 'completed'];

        $totalUsers = User::count();
        $totalSellers = User::where('role', User::ROLE_SELLER)->count();
        $totalProducts = Product::count();
        $pendingProducts = Product::where('approval_status', 'pending')->count();
        $pendingSellerRequests = SellerRequest::where('status', 'pending')->count();
        $totalOrders = Order::count();
        $pendingOrders = Order::whereIn('status', ['pending', 'waiting_payment'])->count();

        $totalTransactions = Order::whereIn('status', $activeStatuses)->sum('total');
        $totalCommission = Commission::sum('commission_amount');
        $platformRevenue = Commission::where('status', 'paid')->sum('commission_amount');

        $bestSellers = Product::with(['primaryImage'])
            ->withCount(['orderItems as sold_count' => fn ($q) => $q->whereHas('order', fn ($o) => $o->whereIn('status', $activeStatuses))])
            ->orderByDesc('sold_count')
            ->take(5)
            ->get();

        $topSellers = User::where('role', User::ROLE_SELLER)
            ->whereHas('shop')
            ->with('shop')
            ->get()
            ->map(function (User $seller) use ($activeStatuses) {
                $revenue = (int) DB::table('order_items')
                    ->join('orders', 'orders.id', '=', 'order_items.order_id')
                    ->where('order_items.seller_id', $seller->id)
                    ->whereIn('orders.status', $activeStatuses)
                    ->sum('order_items.subtotal');
                $seller->revenue = $revenue;

                return $seller;
            })
            ->filter(fn ($seller) => $seller->revenue > 0)
            ->sortByDesc('revenue')
            ->take(5)
            ->values();

        $daily = collect(range(6, 0))->mapWithKeys(fn ($i) => [
            now()->subDays($i)->format('d M') => (int) Order::whereIn('status', $activeStatuses)
                ->whereDate('created_at', now()->subDays($i)->toDateString())
                ->sum('total'),
        ]);

        $weekly = collect(range(3, 0))->mapWithKeys(function ($i) use ($activeStatuses) {
            $week = now()->subWeeks($i);

            return [$week->startOfWeek()->format('d M') => (int) Order::whereIn('status', $activeStatuses)
                ->whereBetween('created_at', [$week->copy()->startOfWeek(), $week->copy()->endOfWeek()])
                ->sum('total'),
            ];
        });

        $monthly = collect(range(5, 0))->mapWithKeys(function ($i) use ($activeStatuses) {
            $month = now()->subMonths($i);

            return [$month->format('M Y') => (int) Order::whereIn('status', $activeStatuses)
                ->whereBetween('created_at', [$month->copy()->startOfMonth(), $month->copy()->endOfMonth()])
                ->sum('total'),
            ];
        });

        return view('admin.dashboard', compact(
            'totalUsers', 'totalSellers', 'totalProducts', 'pendingProducts',
            'pendingSellerRequests', 'totalOrders', 'pendingOrders',
            'totalTransactions', 'totalCommission', 'platformRevenue',
            'bestSellers', 'topSellers', 'daily', 'weekly', 'monthly'
        ));
    }
}
