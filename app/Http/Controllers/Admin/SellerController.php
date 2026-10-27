<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SellerController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', User::ROLE_SELLER)
            ->with('shop')
            ->withCount(['products' => fn ($q) => $q->where('approval_status', 'approved')]);

        $query->when($request->filled('search'), function ($q) use ($request) {
            $q->where(function ($inner) use ($request) {
                $inner->where('name', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%")
                    ->orWhereHas('shop', fn ($s) => $s->where('name', 'like', "%{$request->search}%"));
            });
        });

        $sellers = $query->latest()->paginate(15)->withQueryString();

        return view('admin.sellers.index', compact('sellers'));
    }

    public function show(User $seller)
    {
        if ($seller->role !== User::ROLE_SELLER) {
            abort(404);
        }

        $seller->load(['shop', 'products' => fn ($q) => $q->latest()->limit(10)]);

        $stats = [
            'Produk' => Product::where('seller_id', $seller->id)->count(),
            'Produk Aktif' => Product::where('seller_id', $seller->id)->where('approval_status', 'approved')->where('status', 1)->count(),
            'Produk Pending' => Product::where('seller_id', $seller->id)->where('approval_status', 'pending')->count(),
            'Total Penjualan' => (int) DB::table('order_items')
                ->join('orders', 'orders.id', '=', 'order_items.order_id')
                ->where('order_items.seller_id', $seller->id)
                ->whereIn('orders.status', ['paid', 'processed', 'packing', 'shipped', 'delivered', 'completed'])
                ->sum('order_items.subtotal'),
            'Komisi' => (int) Commission::where('seller_id', $seller->id)->sum('commission_amount'),
        ];

        return view('admin.sellers.show', compact('seller', 'stats'));
    }

    public function suspend(User $seller): RedirectResponse
    {
        if ($seller->role !== User::ROLE_SELLER) {
            abort(404);
        }

        $shop = Shop::where('user_id', $seller->id)->first();

        if ($shop) {
            $shop->update(['is_active' => ! $shop->is_active]);
        }

        $seller->update([
            'is_active' => $shop?->is_active ?? ! $seller->is_active,
            'suspended_at' => $shop?->is_active ? null : now(),
        ]);

        return back()->with('success', 'Status seller diperbarui.');
    }
}
