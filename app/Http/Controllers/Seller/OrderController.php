<?php

namespace App\Http\Controllers\Seller;

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
        $query = Order::with(['items.product', 'items.variant', 'payment', 'user', 'latestTracking'])
            ->forSeller(auth()->id())
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->latest();

        $orders = $query->paginate(12)->withQueryString();

        return view('seller.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        if (! $order->items()->where('seller_id', auth()->id())->exists()) {
            abort(403);
        }

        $order->load(['items.product', 'items.variant', 'items.review', 'payment', 'user', 'address', 'tracking']);

        return view('seller.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        if (! $order->items()->where('seller_id', auth()->id())->exists()) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => 'required|in:processed,packing,shipped',
        ]);

        $allowed = [
            'paid' => ['processed'],
            'processed' => ['packing'],
            'packing' => ['shipped'],
        ];

        if (! isset($allowed[$order->status]) || ! in_array($validated['status'], $allowed[$order->status], true)) {
            return back()->with('error', 'Transisi status pesanan tidak valid.');
        }

        $order->update(['status' => $validated['status']]);

        OrderTracking::create([
            'order_id' => $order->id,
            'status' => $validated['status'],
            'note' => $this->statusNote($validated['status']),
        ]);

        $order->user->notify(new MarketplaceNotification(
            'Status Pesanan Diperbarui',
            "Pesanan {$order->order_number} sekarang: {$order->statusLabel()}.",
            route('orders.show', $order)
        ));

        return back()->with('success', 'Status pesanan diperbarui.');
    }

    private function statusNote(string $status): string
    {
        return match ($status) {
            'processed' => 'Pesanan sedang diproses seller.',
            'packing' => 'Pesanan sedang dikemas.',
            default => 'Pesanan dikirim ke alamat pembeli.',
        };
    }
}
