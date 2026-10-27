<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderTracking;
use App\Models\Payment;
use App\Notifications\MarketplaceNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with(['order.user'])
            ->when($request->filled('status'), fn ($q) => $q->where('payment_status', $request->status))
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where('transaction_id', 'like', "%{$request->search}%");
            })
            ->latest();

        $payments = $query->paginate(15)->withQueryString();

        return view('admin.payments.index', compact('payments'));
    }

    public function refund(Payment $payment): RedirectResponse
    {
        if ($payment->payment_status !== Payment::STATUS_PAID) {
            return back()->with('error', 'Hanya pembayaran berstatus Dibayar yang dapat di-refund.');
        }

        $payment->update([
            'payment_status' => Payment::STATUS_REFUNDED,
            'refunded_at' => now(),
        ]);

        $order = $payment->order;
        $order->update(['status' => 'refunded']);

        OrderTracking::create([
            'order_id' => $order->id,
            'status' => 'refunded',
            'note' => 'Pembayaran di-refund oleh Admin.',
        ]);

        $order->user->notify(new MarketplaceNotification(
            'Pembayaran Dikembalikan',
            "Pembayaran untuk pesanan {$order->order_number} telah di-refund oleh admin.",
            route('orders.show', $order)
        ));

        return back()->with('success', 'Pembayaran berhasil di-refund.');
    }
}
