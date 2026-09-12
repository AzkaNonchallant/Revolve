<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use App\Models\Wishlist;
use App\Models\WishlistItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index()
    {
        $wishlist = Wishlist::with(['items' => fn ($q) => $q->with(['product.category', 'product.seller.shop', 'variant'])])
            ->firstOrCreate(['user_id' => Auth::id()]);

        return view('wishlist.index', compact('wishlist'));
    }

    public function toggle(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'nullable|exists:product_variants,id',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        if (! $product->published()->exists()) {
            return back()->with('error', 'Produk tidak tersedia.');
        }

        $wishlist = Wishlist::firstOrCreate(['user_id' => Auth::id()]);

        $existing = $wishlist->items()
            ->where('product_id', $product->id)
            ->where('variant_id', $validated['variant_id'] ?? null)
            ->first();

        if ($existing) {
            $existing->delete();

            return back()->with('success', 'Produk dihapus dari wishlist.');
        }

        $wishlist->items()->create([
            'product_id' => $product->id,
            'variant_id' => $validated['variant_id'] ?? null,
        ]);

        return back()->with('success', 'Produk ditambahkan ke wishlist.');
    }

    public function destroy(WishlistItem $wishlistItem): RedirectResponse
    {
        if ($wishlistItem->wishlist->user_id !== Auth::id()) {
            abort(403);
        }

        $wishlistItem->delete();

        return back()->with('success', 'Produk dihapus dari wishlist.');
    }

    public function moveToCart(WishlistItem $wishlistItem, Request $request): RedirectResponse
    {
        if ($wishlistItem->wishlist->user_id !== Auth::id()) {
            abort(403);
        }

        $wishlistItem->load(['product', 'variant']);

        $product = $wishlistItem->product;
        $variant = $wishlistItem->variant;

        if (! $product->published()->exists()) {
            return back()->with('error', 'Produk tidak tersedia.');
        }

        $available = $variant ? $variant->stock : $product->stock;
        if ($available < 1) {
            return back()->with('error', 'Stok produk tidak mencukupi.');
        }

        $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);

        $existing = $cart->items()
            ->where('product_id', $product->id)
            ->where('variant_id', $variant?->id)
            ->first();

        if ($existing) {
            $existing->increment('quantity');
        } else {
            $cart->items()->create([
                'product_id' => $product->id,
                'variant_id' => $variant?->id,
                'quantity' => 1,
            ]);
        }

        $wishlistItem->delete();

        return back()->with('success', 'Produk dipindahkan ke keranjang.');
    }
}
