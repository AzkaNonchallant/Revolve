<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $cart = Cart::with(['items' => fn ($q) => $q->with(['product.category', 'variant'])])
            ->firstOrCreate(['user_id' => Auth::id()]);

        $subtotal = $cart->items->filter->is_selected->sum(fn ($item) => $item->effectivePrice() * $item->quantity);

        return view('cart.index', compact('cart', 'subtotal'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'nullable|exists:product_variants,id',
            'quantity' => 'nullable|integer|min:1',
        ]);

        $product = Product::find($validated['product_id']);

        if (! $product || ! $product->published()->exists()) {
            return back()->with('error', 'Produk tidak tersedia');
        }

        $quantity = $validated['quantity'] ?? 1;

        $variant = null;
        if (! empty($validated['variant_id'])) {
            $variant = ProductVariant::where('id', $validated['variant_id'])
                ->where('product_id', $product->id)
                ->where('is_active', true)
                ->first();

            if (! $variant) {
                return back()->with('error', 'Varian produk tidak valid');
            }

            if ($variant->stock < $quantity) {
                return back()->with('error', 'Stok varian tidak mencukupi');
            }
        } elseif ($product->variants()->where('is_active', true)->exists() && $product->stock < $quantity) {
            return back()->with('error', 'Silakan pilih varian produk terlebih dahulu');
        } elseif ($product->stock < $quantity) {
            return back()->with('error', 'Stok tidak mencukupi');
        }

        $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);

        $existingItem = $cart->items()
            ->where('product_id', $product->id)
            ->where('variant_id', $variant?->id)
            ->first();

        if ($existingItem) {
            $newQuantity = $existingItem->quantity + $quantity;
            $available = $variant ? $variant->stock : $product->stock;

            if ($available < $newQuantity) {
                return back()->with('error', 'Stok tidak mencukupi');
            }

            $existingItem->update(['quantity' => $newQuantity]);
        } else {
            $cart->items()->create([
                'product_id' => $product->id,
                'variant_id' => $variant?->id,
                'quantity' => $quantity,
            ]);
        }

        return back()->with('success', 'Produk ditambahkan ke keranjang');
    }

    public function update(Request $request, CartItem $cartItem): RedirectResponse
    {
        if ($cartItem->cart->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $available = $this->availableStock($cartItem);

        if ($available < $validated['quantity']) {
            return back()->with('error', 'Stok tidak mencukupi');
        }

        $cartItem->update($validated);

        return back()->with('success', 'Keranjang diperbarui');
    }

    public function toggleSelect(CartItem $cartItem): RedirectResponse
    {
        if ($cartItem->cart->user_id !== Auth::id()) {
            abort(403);
        }

        $cartItem->update(['is_selected' => ! $cartItem->is_selected]);

        return back();
    }

    public function destroy(CartItem $cartItem): RedirectResponse
    {
        if ($cartItem->cart->user_id !== Auth::id()) {
            abort(403);
        }

        $cartItem->delete();

        return back()->with('success', 'Produk dihapus dari keranjang');
    }

    public function clear(): RedirectResponse
    {
        $cart = Cart::where('user_id', Auth::id())->first();

        if ($cart) {
            $cart->items()->delete();
        }

        return back()->with('success', 'Keranjang dikosongkan');
    }

    private function availableStock(CartItem $item): int
    {
        if ($item->variant) {
            return $item->variant->stock;
        }

        $hasVariants = $item->product->variants()->where('is_active', true)->exists();

        return $hasVariants ? 0 : $item->product->stock;
    }
}
