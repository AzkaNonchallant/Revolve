<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Shop;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'brand', 'seller.shop', 'primaryImage'])
            ->published();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('description', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('category')) {
            $category = Category::find($request->category);

            if ($category) {
                if ($category->isParent()) {
                    $childIds = $category->children()->pluck('id');
                    $query->whereIn('category_id', $childIds->push($category->id));
                } else {
                    $query->where('category_id', $category->id);
                }
            }
        }

        if ($request->filled('brand')) {
            $query->whereIn('brand_id', (array) $request->input('brand'));
        }

        if ($request->filled('seller')) {
            $query->where('seller_id', $request->seller);
        }

        if ($request->filled('size')) {
            $query->whereHas('variants', fn ($q) => $q->where('size', $request->size)->where('is_active', true));
        }

        if ($request->filled('color')) {
            $query->whereHas('variants', fn ($q) => $q->where('color', $request->color)->where('is_active', true));
        }

        if ($request->filled('min_price')) {
            $query->where(function ($q) use ($request) {
                $q->where('price', '>=', $request->min_price)
                    ->orWhere('discount_price', '>=', $request->min_price);
            });
        }

        if ($request->filled('max_price')) {
            $query->where(function ($q) use ($request) {
                $q->where('price', '<=', $request->max_price)
                    ->orWhere('discount_price', '<=', $request->max_price);
            });
        }

        if ($request->filled('min_rating')) {
            $rating = (float) $request->min_rating;
            $query->whereHas('reviews', fn ($q) => $q->where('is_hidden', false), '>=', 1, 'and')
                ->withAvg('reviews as avg_rating', 'rating')
                ->having('avg_rating', '>=', $rating);
        }

        $query->withCount(['orderItems as sold_count' => fn ($q) => $q->whereHas('order', fn ($o) => $o->whereIn('status', ['paid', 'processed', 'packing', 'shipped', 'delivered', 'completed']))]);

        switch ($request->get('sort')) {
            case 'price_asc':
                $query->orderByRaw('COALESCE(discount_price, price) ASC');
                break;
            case 'price_desc':
                $query->orderByRaw('COALESCE(discount_price, price) DESC');
                break;
            case 'rating':
                $query->orderByDesc('sold_count');
                break;
            case 'terlaris':
                $query->orderByDesc('sold_count');
                break;
            case 'oldest':
                $query->oldest();
                break;
            default:
                $query->latest();
                break;
        }

        $products = $query->paginate(12)->withQueryString();

        $categories = Category::parents()->with('children')->get();
        $brands = Brand::where('is_active', true)->get();
        $sizes = ['S', 'M', 'L', 'XL', '40', '41', '42', '43', '44'];
        $colors = ['Hitam', 'Putih', 'Merah', 'Biru', 'Abu-abu', 'Cokelat', 'Krem'];

        return view('products.index', compact(
            'products', 'categories', 'brands', 'sizes', 'colors'
        ));
    }

    public function show(Product $product)
    {
        if (! $product->published()->exists()) {
            abort(404);
        }

        $product->load([
            'category', 'brand', 'seller.shop', 'images', 'variants',
            'reviews' => fn ($q) => $q->where('is_hidden', false)->latest(),
            'reviews.user',
        ]);

        $avgRating = round((float) $product->reviews->avg('rating'), 1);
        $ratingCounts = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
        foreach ($product->reviews as $review) {
            $ratingCounts[$review->rating]++;
        }

        $relatedProducts = Product::with(['category', 'primaryImage'])
            ->published()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->latest()
            ->take(4)
            ->get();

        $isWishlisted = false;
        if (auth()->check()) {
            $isWishlisted = auth()->user()->wishlist?->items()
                ->where('product_id', $product->id)
                ->where('variant_id', null)
                ->exists() ?? false;
        }

        $soldCount = $product->soldCount();

        return view('products.show', compact(
            'product', 'relatedProducts', 'avgRating', 'ratingCounts', 'isWishlisted', 'soldCount'
        ));
    }

    public function category(Category $category)
    {
        $query = Product::published()->with(['category', 'brand', 'primaryImage']);

        if ($category->isParent()) {
            $childIds = $category->children()->pluck('id');
            $query->whereIn('category_id', $childIds->push($category->id));
        } else {
            $query->where('category_id', $category->id);
        }

        $products = $query->latest()->paginate(12);

        return view('products.index', compact('products', 'category'));
    }

    public function shop(Shop $shop)
    {
        $products = Product::with(['category', 'primaryImage', 'reviews'])
            ->published()
            ->where('seller_id', $shop->user_id)
            ->withCount(['orderItems as sold_count' => fn ($q) => $q->whereHas('order', fn ($o) => $o->whereIn('status', ['paid', 'processed', 'packing', 'shipped', 'delivered', 'completed']))])
            ->latest()
            ->paginate(12);

        $productIds = $products->pluck('id');

        return view('products.shop', compact('shop', 'products'));
    }
}
