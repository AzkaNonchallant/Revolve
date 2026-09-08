<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderTracking;
use App\Models\Address;
use App\Models\ShippingRate;
use App\Models\Courier;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function show()
    {
        $cart = Cart::with('items.product')->where('user_id', Auth::id())->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang kosong');
        }

        $addresses = Address::where('user_id', Auth::id())->get();
        $couriers = Courier::with('shippingRates')->get();

        $subtotal = $cart->items->sum(fn($item) => $item->product->price * $item->quantity);
        $totalWeight = $cart->items->sum(fn($item) => $item->product->weight * $item->quantity);

        return view('checkout.show', compact('cart', 'addresses', 'couriers', 'subtotal', 'totalWeight'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'address_id' => 'required|exists:addresses,id',
            'courier_id' => 'nullable|exists:couriers,id',
            'shipping_rate_id' => 'nullable|exists:shipping_rates,id',
            'payment_method' => 'required|string|max:100',
            'delivery_schedule' => 'nullable|date',
        ]);

        $cart = Cart::with('items.product')->where('user_id', Auth::id())->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang kosong');
        }

        $address = Address::findOrFail($validated['address_id']);
        if ($address->user_id !== Auth::id()) {
            return back()->with('error', 'Alamat tidak valid');
        }

        $shippingCost = 0;
        if ($validated['shipping_rate_id']) {
            $shippingRate = ShippingRate::findOrFail($validated['shipping_rate_id']);
            $totalWeightKg = ceil($cart->items->sum(fn($item) => $item->product->weight * $item->quantity) / 1000);
            $shippingCost = $shippingRate->rate_per_kg * max($totalWeightKg, 1);
        }

        $subtotal = $cart->items->sum(fn($item) => $item->product->price * $item->quantity);
        $total = $subtotal + $shippingCost;

        $order = DB::transaction(function () use ($cart, $address, $validated, $subtotal, $shippingCost, $total) {
            $order = Order::create([
                'user_id' => Auth::id(),
                'address_id' => $address->id,
                'courier_id' => $validated['courier_id'],
                'shipping_rate_id' => $validated['shipping_rate_id'],
                'order_number' => 'ORD-' . Str::upper(Str::random(10)),
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'discount' => 0,
                'total' => $total,
                'payment_method' => $validated['payment_method'],
                'delivery_schedule' => $validated['delivery_schedule'],
                'status' => 'waiting_payment',
            ]);

            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'price' => $item->product->price,
                    'quantity' => $item->quantity,
                    'subtotal' => $item->product->price * $item->quantity,
                ]);

                $item->product->decrement('stock', $item->quantity);
            }

            OrderTracking::create([
                'order_id' => $order->id,
                'status' => 'waiting_payment',
                'note' => 'Order dibuat, menunggu pembayaran',
            ]);

            $cart->items()->delete();

            return $order;
        });

        return redirect()->route('checkout.success', $order);
    }

    public function success(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        return view('checkout.success', compact('order'));
    }
}