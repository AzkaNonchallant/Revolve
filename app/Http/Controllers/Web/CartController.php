<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $cart = Cart::with('items.product.category')->firstOrCreate([
            'user_id' => Auth::id()
        ]);

        return view('cart.index', compact('cart'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'nullable|integer|min:1',
        ]);

        $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);
        $product = Product::findOrFail($validated['product_id']);

        if ($product->stock < ($validated['quantity'] ?? 1)) {
            return back()->with('error', 'Stok tidak mencukupi');
        }

        $existingItem = $cart->items()->where('product_id', $validated['product_id'])->first();

        if ($existingItem) {
            $newQuantity = $existingItem->quantity + ($validated['quantity'] ?? 1);
            if ($product->stock < $newQuantity) {
                return back()->with('error', 'Stok tidak mencukupi');
            }
            $existingItem->update(['quantity' => $newQuantity]);
        } else {
            $cart->items()->create([
                'product_id' => $validated['product_id'],
                'quantity' => $validated['quantity'] ?? 1,
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

        $product = Product::findOrFail($cartItem->product_id);

        if ($product->stock < $validated['quantity']) {
            return back()->with('error', 'Stok tidak mencukupi');
        }

        $cartItem->update($validated);

        return back()->with('success', 'Keranjang diperbarui');
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
}