<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductApproval;
use App\Models\ProductVariant;
use App\Models\User;
use App\Notifications\MarketplaceNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'brand', 'primaryImage'])
            ->where('seller_id', auth()->id())
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('approval'), fn ($q) => $q->where('approval_status', $request->approval))
            ->latest();

        $products = $query->paginate(12)->withQueryString();

        return view('seller.products.index', compact('products'));
    }

    public function pending()
    {
        $products = Product::with(['category', 'primaryImage'])
            ->where('seller_id', auth()->id())
            ->where('approval_status', 'pending')
            ->latest()
            ->paginate(12);

        return view('seller.products.pending', compact('products'));
    }

    public function create()
    {
        $categories = Category::parents()->with('children')->get();
        $brands = Brand::where('is_active', true)->get();

        return view('seller.products.create', compact('categories', 'brands'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatedData($request);

        $validated['seller_id'] = auth()->id();
        $validated['slug'] = $this->makeSlug($validated['name']);
        $validated['approval_status'] = Product::APPROVAL_PENDING;
        $validated['status'] = 1;

        $product = DB::transaction(function () use ($validated, $request) {
            $product = Product::create($validated);

            $this->saveVariants($product, $request);
            $this->saveImages($product, $request);

            ProductApproval::create([
                'product_id' => $product->id,
                'action' => 'submit',
                'note' => 'Produk baru diajukan untuk review.',
            ]);

            foreach (User::where('role', User::ROLE_ADMIN)->get() as $admin) {
                $admin->notify(new MarketplaceNotification(
                    'Produk Baru Menunggu Approval',
                    "Produk '{$product->name}' menunggu review Admin.",
                    route('admin.products.show', $product)
                ));
            }

            return $product;
        });

        return redirect()->route('seller.products.index')->with('success', 'Produk dibuat dan sedang menunggu approval Admin.');
    }

    public function edit(Product $product)
    {
        $this->authorizeSeller($product);

        $product->load(['variants', 'images', 'brand', 'category']);

        $categories = Category::parents()->with('children')->get();
        $brands = Brand::where('is_active', true)->get();

        return view('seller.products.edit', compact('product', 'categories', 'brands'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $this->authorizeSeller($product);

        $validated = $this->validatedData($request);

        $importantFields = $this->changedImportantFields($product, $validated);

        $product = DB::transaction(function () use ($product, $validated, $request, $importantFields) {
            $product->slug = $this->makeSlug($validated['name'], $product);

            if (! empty($importantFields)) {
                $product->approval_status = Product::APPROVAL_PENDING;
                $product->approval_note = null;
                $product->approved_at = null;
            }

            $product->update(array_merge($validated, ['slug' => $product->slug]));

            $this->saveVariants($product, $request, true);
            $this->saveImages($product, $request);

            if (! empty($importantFields)) {
                ProductApproval::create([
                    'product_id' => $product->id,
                    'action' => 'submit',
                    'note' => 'Perubahan data penting: '.implode(', ', $importantFields).'. Produk menunggu review ulang.',
                ]);

                foreach (User::where('role', User::ROLE_ADMIN)->get() as $admin) {
                    $admin->notify(new MarketplaceNotification(
                        'Produk Menunggu Review Ulang',
                        "Produk '{$product->name}' menunggu approval ulang karena data penting berubah.",
                        route('admin.products.show', $product)
                    ));
                }
            }

            return $product;
        });

        $message = empty($importantFields)
            ? 'Produk diperbarui.'
            : 'Produk diperbarui. Data penting berubah, produk kembali menunggu approval Admin.';

        return redirect()->route('seller.products.index')->with(
            empty($importantFields) ? 'success' : 'warning',
            $message
        );
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->authorizeSeller($product);

        $product->delete();

        return back()->with('success', 'Produk dihapus.');
    }

    public function submitForApproval(Product $product): RedirectResponse
    {
        $this->authorizeSeller($product);

        if ($product->approval_status === Product::APPROVAL_APPROVED && $product->isActive()) {
            return back()->with('error', 'Produk sudah disetujui dan aktif.');
        }

        $product->update([
            'approval_status' => Product::APPROVAL_PENDING,
            'approval_note' => null,
            'approved_at' => null,
            'status' => 1,
        ]);

        ProductApproval::create([
            'product_id' => $product->id,
            'action' => 'submit',
            'note' => 'Produk diajukan ulang oleh seller.',
        ]);

        foreach (User::where('role', User::ROLE_ADMIN)->get() as $admin) {
            $admin->notify(new MarketplaceNotification(
                'Produk Menunggu Approval',
                "Produk '{$product->name}' diajukan ulang oleh seller.",
                route('admin.products.show', $product)
            ));
        }

        return back()->with('success', 'Produk diajukan untuk approval ulang.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:150',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'description' => 'nullable|string|max:5000',
            'material' => 'nullable|string|max:150',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0|lt:price',
            'stock' => 'required|integer|min:0',
            'weight' => 'required|integer|min:1',
            'status' => 'nullable|boolean',
            'images' => 'nullable|array|max:6',
            'images.*' => 'image|max:2048',
            'sizes' => 'nullable|array',
            'colors' => 'nullable|array',
            'skus' => 'nullable|array',
            'variant_prices' => 'nullable|array',
            'variant_stocks' => 'nullable|array',
        ]);
    }

    private function makeSlug(string $name, ?Product $existing = null): string
    {
        $slug = Str::slug($name);
        $query = Product::where('slug', $slug);

        if ($existing) {
            $query->where('id', '!=', $existing->id);
        }

        if ($query->exists()) {
            $slug .= '-'.Str::lower(Str::random(5));
        }

        return $slug;
    }

    private function saveVariants(Product $product, Request $request, bool $replace = false): void
    {
        if ($replace) {
            $product->variants()->delete();
        }

        $sizes = $request->input('sizes', []);
        $colors = $request->input('colors', []);
        $skus = $request->input('skus', []);
        $prices = $request->input('variant_prices', []);
        $stocks = $request->input('variant_stocks', []);

        $rows = max(count($sizes), count($colors));

        for ($i = 0; $i < $rows; $i++) {
            $size = isset($sizes[$i]) ? trim((string) $sizes[$i]) : null;
            $color = isset($colors[$i]) ? trim((string) $colors[$i]) : null;

            if ($size === '' && $color === '') {
                continue;
            }

            ProductVariant::create([
                'product_id' => $product->id,
                'size' => $size !== '' ? $size : null,
                'color' => $color !== '' ? $color : null,
                'sku' => isset($skus[$i]) ? trim((string) $skus[$i]) : null,
                'price' => isset($prices[$i]) && $prices[$i] !== '' ? $prices[$i] : null,
                'stock' => isset($stocks[$i]) ? (int) $stocks[$i] : 0,
                'is_active' => true,
            ]);
        }
    }

    private function saveImages(Product $product, Request $request): void
    {
        if ($request->hasFile('images')) {
            $first = true;

            foreach ($request->file('images') as $file) {
                $product->images()->create([
                    'path' => $file->store('products', 'public'),
                    'is_primary' => $first,
                    'sort_order' => $first ? 0 : $product->images()->count(),
                ]);

                $first = false;
            }

            if ($product->image === null) {
                $primary = $product->images()->orderBy('sort_order')->first();
                if ($primary) {
                    $product->update(['image' => $primary->path]);
                }
            }
        }
    }

    private function authorizeSeller(Product $product): void
    {
        if ($product->seller_id !== auth()->id()) {
            abort(403);
        }
    }

    private function changedImportantFields(Product $product, array $validated): array
    {
        $important = ['name', 'description', 'price', 'discount_price', 'category_id', 'brand_id', 'material', 'stock'];
        $changed = [];

        foreach ($important as $field) {
            $old = (string) $product->{$field};
            $new = (string) ($validated[$field] ?? $product->{$field});

            if ($old !== $new) {
                $changed[] = $field;
            }
        }

        return $changed;
    }
}
