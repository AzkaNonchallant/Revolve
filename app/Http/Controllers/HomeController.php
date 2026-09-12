<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $heroBanners = Banner::with('category')
            ->where('is_active', true)
            ->where('type', 'hero')
            ->orderBy('sort_order')
            ->take(3)
            ->get();

        $discountProducts = Product::with(['category', 'primaryImage'])
            ->forCustomer()
            ->whereNotNull('discount_price')
            ->whereColumn('discount_price', '<', 'price')
            ->orderByRaw('(price - discount_price) DESC')
            ->take(8)
            ->get();

        $latestProducts = Product::with(['category', 'primaryImage'])
            ->forCustomer()
            ->latest()
            ->take(8)
            ->get();

        $categories = Category::withCount('products')->get();

        return view('home', compact('heroBanners', 'discountProducts', 'latestProducts', 'categories'));
    }
}
