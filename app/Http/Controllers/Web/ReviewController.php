<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Notifications\MarketplaceNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request, Order $order, OrderItem $orderItem): RedirectResponse
    {
        if ($order->user_id !== Auth::id() || $orderItem->order_id !== $order->id) {
            abort(403);
        }

        if ($order->status !== 'completed') {
            return back()->with('error', 'Review hanya dapat diberikan setelah pesanan selesai.');
        }

        if ($orderItem->review()->exists()) {
            return back()->with('error', 'Anda sudah memberikan review untuk produk ini.');
        }

        $validated = $request->validate([
            'rating' => 'required|integer|between:1,5',
            'review' => 'nullable|string|max:1000',
            'image' => 'nullable|image|max:2048',
        ]);

        $path = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('reviews', 'public');
        }

        $review = $orderItem->review()->create([
            'product_id' => $orderItem->product_id,
            'user_id' => Auth::id(),
            'order_id' => $order->id,
            'order_item_id' => $orderItem->id,
            'rating' => $validated['rating'],
            'review' => $validated['review'] ?? null,
            'image' => $path,
        ]);

        $sellerId = $orderItem->seller_id;
        if ($sellerId) {
            User::find($sellerId)?->notify(new MarketplaceNotification(
                'Review Baru',
                "Produk '{$orderItem->product->name}' menerima rating {$review->rating}/5.",
                route('seller.products.index')
            ));
        }

        return back()->with('success', 'Terima kasih atas review Anda.');
    }
}
