<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function edit()
    {
        $shop = auth()->user()->shop;
        $categories = collect();

        if ($shop) {
            $categories = Category::parents()->get();
        }

        return view('seller.shop.edit', compact('shop', 'categories'));
    }

    public function update(Request $request): RedirectResponse
    {
        $shop = auth()->user()->shop;

        if (! $shop) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:120',
            'slug' => 'required|string|max:140|alpha_dash|unique:shops,slug,'.$shop->id,
            'description' => 'nullable|string|max:2000',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:1000',
            'category_id' => 'nullable|exists:categories,id',
            'logo' => 'nullable|image|max:2048',
            'banner' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('shops', 'public');
        }

        if ($request->hasFile('banner')) {
            $validated['banner'] = $request->file('banner')->store('shops/banners', 'public');
        }

        $shop->update($validated);

        return back()->with('success', 'Profil toko diperbarui.');
    }
}
