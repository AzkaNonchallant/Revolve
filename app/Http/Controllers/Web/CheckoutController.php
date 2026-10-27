<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Cart;
use App\Models\Courier;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderTracking;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\ShippingRate;
use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherUsage;
use App\Notifications\MarketplaceNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function show()
    {
        $cart = Cart::with(['items' => fn ($q) => $q->with(['product.category', 'variant', 'product.seller'])])
            ->where('user_id', Auth::id())
            ->first();

        $items = $cart?->items?->where('is_selected', true)->values() ?? collect();

        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Silakan pilih produk terlebih dahulu untuk checkout');
        }

        $addresses = Address::where('user_id', Auth::id())->get();
        $couriers = Courier::with('shippingRates')->get();

        $subtotalRaw = $items->sum(fn ($item) => $item->effectivePrice() * $item->quantity);
        $totalWeight = $items->sum(fn ($item) => ($item->product?->weight ?? 0) * $item->quantity);

        $availableVoucherIds = $this->applicableVouchers($items, (int) $subtotalRaw)->pluck('id');
        $vouchers = Voucher::active()->whereIn('id', $availableVoucherIds)->orderByDesc('value')->get();

        return view('checkout.show', compact('cart', 'items', 'addresses', 'couriers', 'subtotalRaw', 'totalWeight', 'vouchers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'address_id' => 'required|exists:addresses,id',
            'courier_id' => 'nullable|exists:couriers,id',
            'shipping_rate_id' => 'nullable|exists:shipping_rates,id',
            'payment_method' => 'required|string|max:100',
            'voucher_id' => 'nullable|exists:vouchers,id',
        ]);

        $cart = Cart::with(['items' => fn ($q) => $q->with(['product.category', 'variant'])])
            ->where('user_id', Auth::id())
            ->first();

        $items = $cart?->items?->where('is_selected', true)->values() ?? collect();

        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang kosong');
        }

        $address = Address::findOrFail($validated['address_id']);
        if ($address->user_id !== Auth::id()) {
            return back()->with('error', 'Alamat tidak valid');
        }

        $prepared = [];
        $subtotal = 0;
        $totalWeight = 0;

        foreach ($items as $item) {
            $product = $item->product;

            if (! $product || ! $product->published()->exists()) {
                return back()->with('error', "Produk '{$product?->name}' tidak tersedia.");
            }

            $available = $item->variant ? $item->variant->stock : $product->stock;

            if ($available < $item->quantity) {
                return back()->with('error', "Stok untuk '{$product->name}' tidak mencukupi. Tersedia {$available}.");
            }

            $price = $item->effectivePrice();

            $prepared[] = [
                'item' => $item,
                'price' => $price,
                'subtotal' => (int) round($price * $item->quantity),
            ];

            $subtotal += (int) round($price * $item->quantity);
            $totalWeight += ($product->weight ?? 0) * $item->quantity;
        }

        $voucher = null;
        if (! empty($validated['voucher_id'])) {
            $voucher = Voucher::find($validated['voucher_id']);

            if (! $voucher || ! $voucher->isValidFor((int) $subtotal)) {
                return back()->with('error', 'Voucher tidak valid atau tidak memenuhi syarat.');
            }

            $valid = $this->voucherAppliesToItems($voucher, collect($prepared)->pluck('item'));

            if (! $valid) {
                return back()->with('error', 'Voucher tidak berlaku untuk produk pada keranjang.');
            }
        }

        $voucherDiscount = $voucher ? $voucher->discountAmountFor((int) $subtotal) : 0;

        $shippingCost = 0;
        if (! empty($validated['shipping_rate_id'])) {
            $rate = ShippingRate::findOrFail($validated['shipping_rate_id']);
            $weightKg = max(1, (int) ceil($totalWeight / 1000));
            $shippingCost = (int) round($rate->rate_per_kg * $weightKg);
        }

        $total = max(0, $subtotal - $voucherDiscount + $shippingCost);

        $commissionRate = (float) Setting::get('commission_rate', 5);

        $order = DB::transaction(function () use (
            $prepared, $subtotal, $shippingCost, $voucherDiscount, $total,
            $validated, $address, $voucher, $commissionRate
        ) {
            $order = Order::create([
                'user_id' => Auth::id(),
                'address_id' => $address->id,
                'courier_id' => ! empty($validated['courier_id']) ? $validated['courier_id'] : null,
                'shipping_rate_id' => ! empty($validated['shipping_rate_id']) ? $validated['shipping_rate_id'] : null,
                'voucher_id' => $voucher?->id,
                'voucher_code' => $voucher?->code,
                'order_number' => 'ORD-'.strtoupper(Str::random(12)),
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'discount' => 0,
                'voucher_discount' => $voucherDiscount,
                'total' => $total,
                'commission_rate' => $commissionRate,
                'payment_method' => $validated['payment_method'],
                'status' => 'waiting_payment',
            ]);

            $sellerIds = [];

            foreach ($prepared as $prep) {
                $item = $prep['item'];
                $quantity = $item->quantity;

                $itemDiscount = $subtotal > 0
                    ? (int) round($voucherDiscount * ($prep['subtotal'] / $subtotal))
                    : 0;
                $itemSubtotal = $prep['subtotal'] - $itemDiscount;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'seller_id' => $item->product->seller_id,
                    'variant_id' => $item->variant_id,
                    'price' => $prep['price'],
                    'quantity' => $quantity,
                    'discount' => $itemDiscount,
                    'subtotal' => $itemSubtotal,
                ]);

                $item->product->decrement('stock', $quantity);
                if ($item->variant) {
                    $item->variant->decrement('stock', $quantity);
                }

                if ($item->product->seller_id) {
                    $sellerIds[$item->product->seller_id] = $item->product->seller_id;
                }
            }

            Payment::create([
                'order_id' => $order->id,
                'transaction_id' => 'TMP-'.strtoupper(Str::random(14)),
                'payment_method' => $validated['payment_method'],
                'amount' => $total,
                'payment_status' => 'pending',
            ]);

            OrderTracking::create([
                'order_id' => $order->id,
                'status' => 'waiting_payment',
                'note' => 'Pesanan dibuat, menunggu pembayaran.',
            ]);

            if ($voucher) {
                $voucher->increment('used_count');
                VoucherUsage::create([
                    'voucher_id' => $voucher->id,
                    'user_id' => Auth::id(),
                    'order_id' => $order->id,
                    'discount_amount' => $voucherDiscount,
                ]);
            }

            foreach ($sellerIds as $sellerId) {
                User::find($sellerId)?->notify(new MarketplaceNotification(
                    'Pesanan Baru',
                    'Anda menerima pesanan baru. Segera proses pesanan.',
                    route('seller.orders.show', $order)
                ));
            }

            Cart::where('user_id', Auth::id())->first()?->items()
                ->whereIn('id', collect($prepared)->pluck('item')->pluck('id'))
                ->delete();

            return $order;
        });

        return redirect()->route('checkout.success', $order);
    }

    public function success(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $order->load(['items.product', 'items.variant', 'address', 'payment']);

        return view('checkout.success', compact('order'));
    }

    private function applicableVouchers($items, int $subtotal)
    {
        return Voucher::active()->get()->filter(function (Voucher $voucher) use ($items, $subtotal) {
            return $voucher->isValidFor($subtotal) && $this->voucherAppliesToItems($voucher, $items);
        });
    }

    private function voucherAppliesToItems(Voucher $voucher, $items): bool
    {
        $productIds = $items->pluck('product_id');
        $sellerIds = $items->pluck('product')->pluck('seller_id')->filter();
        $categoryIds = $items->pluck('product')->pluck('category_id')->filter();

        return match ($voucher->scope) {
            'category' => $categoryIds->contains($voucher->category_id),
            'seller' => $sellerIds->contains($voucher->seller_id),
            default => true,
        };
    }
}
