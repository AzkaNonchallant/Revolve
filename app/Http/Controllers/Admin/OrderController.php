<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderTracking;
use App\Notifications\MarketplaceNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['user', 'items.product', 'payment', 'latestTracking'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where('order_number', 'like', "%{$request->search}%");
            })
            ->latest();

        $orders = $query->paginate(15)->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'items.product', 'items.variant', 'items.seller', 'payment', 'address', 'courier', 'tracking']);

        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:shipped,completed,cancelled',
        ]);

        $order->update(['status' => $validated['status']]);

        OrderTracking::create([
            'order_id' => $order->id,
            'status' => $validated['status'],
            'note' => 'Status diubah oleh Admin.',
        ]);

        $order->user->notify(new MarketplaceNotification(
            'Status Pesanan Diperbarui',
            "Pesanan {$order->order_number} sekarang: {$order->statusLabel()}.",
            route('orders.show', $order)
        ));

        return back()->with('success', 'Status pesanan diperbarui.');
    }
}
