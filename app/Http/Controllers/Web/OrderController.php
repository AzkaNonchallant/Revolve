<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use App\Models\Order;
use App\Models\OrderTracking;
use App\Models\Payment;
use App\Models\User;
use App\Notifications\MarketplaceNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['items.product', 'address', 'courier', 'payment', 'latestTracking'])
            ->where('user_id', Auth::id())
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->latest();

        $orders = $query->paginate(10)->withQueryString();

        return view('orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $order->load(['items.product', 'items.variant', 'items.review', 'address', 'courier', 'shippingRate', 'payment', 'tracking']);

        return view('orders.show', ['order' => $order]);
    }

    public function pay(Order $order): RedirectResponse
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        if (! $order->isPayable()) {
            return back()->with('error', 'Status pesanan tidak memungkinkan untuk pembayaran.');
        }

        $order->load('items');

        DB::transaction(function () use ($order) {
            $payment = $order->payment ?? Payment::create([
                'order_id' => $order->id,
                'transaction_id' => 'TMP-'.strtoupper(Str::random(14)),
                'payment_method' => $order->payment_method ?: 'transfer',
                'amount' => $order->total,
                'payment_status' => 'pending',
            ]);

            $payment->update([
                'payment_status' => Payment::STATUS_PAID,
                'transaction_id' => 'PAY-'.strtoupper(Str::random(14)),
                'paid_at' => now(),
            ]);

            $order->update(['status' => 'paid']);

            OrderTracking::create([
                'order_id' => $order->id,
                'status' => 'paid',
                'note' => 'Pembayaran berhasil (simulasi payment gateway).',
            ]);

            $rate = (float) $order->commission_rate ?: 5;
            $sellerIds = [];

            foreach ($order->items as $item) {
                if (! $item->seller_id) {
                    continue;
                }

                $commissionAmount = (int) round((float) $item->subtotal * $rate / 100);
                $net = (int) round((float) $item->subtotal - $commissionAmount);

                Commission::updateOrCreate(
                    ['order_item_id' => $item->id],
                    [
                        'order_id' => $order->id,
                        'seller_id' => $item->seller_id,
                        'subtotal' => $item->subtotal,
                        'commission_rate' => $rate,
                        'commission_amount' => $commissionAmount,
                        'net_amount' => $net,
                        'status' => 'pending',
                    ]
                );

                $sellerIds[$item->seller_id] = $item->seller_id;
            }

            foreach ($sellerIds as $sellerId) {
                User::find($sellerId)?->notify(new MarketplaceNotification(
                    'Pesanan Dibayar',
                    "Pesanan {$order->order_number} telah dibayar. Silakan proses pesanan.",
                    route('seller.orders.show', $order)
                ));
            }
        });

        $order->user->notify(new MarketplaceNotification(
            'Pembayaran Berhasil',
            "Pembayaran untuk pesanan {$order->order_number} berhasil.",
            route('orders.show', $order)
        ));

        return redirect()->route('orders.show', $order)->with('success', 'Pembayaran berhasil. Pesanan akan segera diproses seller.');
    }

    public function complete(Order $order): RedirectResponse
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        if (! in_array($order->status, ['shipped', 'delivered'])) {
            return back()->with('error', 'Pesanan belum dapat dikonfirmasi selesai.');
        }

        DB::transaction(function () use ($order) {
            $order->update(['status' => 'completed']);

            OrderTracking::create([
                'order_id' => $order->id,
                'status' => 'completed',
                'note' => 'Pesanan dikonfirmasi selesai oleh pembeli.',
            ]);

            $sellerIds = $order->items->pluck('seller_id')->filter()->unique();
            foreach ($sellerIds as $sellerId) {
                User::find($sellerId)?->notify(new MarketplaceNotification(
                    'Pesanan Selesai',
                    "Pesanan {$order->order_number} telah selesai. Terima kasih."
                ));
            }
        });

        return back()->with('success', 'Pesanan dikonfirmasi selesai. Terima kasih sudah berbelanja!');
    }

    public function cancel(Order $order): RedirectResponse
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        if (! $order->isCancellable()) {
            return back()->with('error', 'Order tidak bisa dibatalkan');
        }

        DB::transaction(function () use ($order) {
            $order->load('items');

            foreach ($order->items as $item) {
                $item->product->increment('stock', $item->quantity);
                if ($item->variant_id) {
                    $item->variant->increment('stock', $item->quantity);
                }
            }

            $order->update(['status' => 'cancelled']);

            OrderTracking::create([
                'order_id' => $order->id,
                'status' => 'cancelled',
                'note' => 'Dibatalkan oleh pembeli.',
            ]);
        });

        return back()->with('success', 'Order berhasil dibatalkan');
    }
}
