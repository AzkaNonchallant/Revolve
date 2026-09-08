<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderTracking;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['items.product', 'address', 'courier', 'shippingRate', 'payment', 'latestTracking'])
            ->where('user_id', Auth::id())
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest();

        $orders = $query->paginate(10)->withQueryString();

        return view('orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        return view('orders.show', ['order' => $order->load(['items.product', 'address', 'courier', 'shippingRate', 'payment', 'tracking'])]);
    }

    public function cancel(Order $order): RedirectResponse
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        if (!in_array($order->status, ['waiting_payment', 'processed'])) {
            return back()->with('error', 'Order tidak bisa dibatalkan');
        }

        $order->update(['status' => 'cancelled']);

        OrderTracking::create([
            'order_id' => $order->id,
            'status' => 'cancelled',
            'note' => 'Dibatalkan oleh user',
        ]);

        return back()->with('success', 'Order berhasil dibatalkan');
    }
}