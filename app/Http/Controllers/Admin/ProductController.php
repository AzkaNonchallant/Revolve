<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductApproval;
use App\Models\User;
use App\Notifications\MarketplaceNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'brand', 'seller.shop', 'primaryImage'])
            ->when($request->filled('approval'), fn ($q) => $q->where('approval_status', $request->approval))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('search'), fn ($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->latest();

        $products = $query->paginate(15)->withQueryString();

        return view('admin.products.index', compact('products'));
    }

    public function show(Product $product)
    {
        $product->load(['category', 'brand', 'seller.shop', 'images', 'variants', 'approvals.reviewer']);

        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $product->load(['category', 'brand']);

        $categories = Category::parents()->with('children')->get();
        $brands = Brand::where('is_active', true)->get();

        return view('admin.products.edit', compact('product', 'categories', 'brands'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'description' => 'nullable|string|max:5000',
            'material' => 'nullable|string|max:150',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0|lt:price',
            'stock' => 'required|integer|min:0',
            'weight' => 'required|integer|min:1',
        ]);

        $validated['slug'] = $product->slug;

        $product->update($validated);

        return redirect()->route('admin.products.show', $product)->with('success', 'Produk diperbarui.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Produk dihapus.');
    }

    public function approve(Product $product): RedirectResponse
    {
        if ($product->approval_status !== 'pending') {
            return back()->with('error', 'Produk tidak dalam status pending.');
        }

        DB::transaction(function () use ($product) {
            $product->update([
                'approval_status' => Product::APPROVAL_APPROVED,
                'approval_note' => null,
                'approved_at' => now(),
                'status' => 1,
            ]);

            ProductApproval::create([
                'product_id' => $product->id,
                'action' => 'approve',
                'note' => 'Produk disetujui Admin.',
                'reviewed_by' => auth()->id(),
            ]);

            if ($product->seller_id) {
                User::find($product->seller_id)?->notify(new MarketplaceNotification(
                    'Produk Disetujui',
                    "Produk '{$product->name}' telah disetujui dan tayang di marketplace.",
                    route('seller.products.index')
                ));
            }
        });

        return back()->with('success', 'Produk disetujui.');
    }

    public function reject(Request $request, Product $product): RedirectResponse
    {
        if ($product->approval_status !== 'pending') {
            return back()->with('error', 'Produk tidak dalam status pending.');
        }

        $validated = $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        DB::transaction(function () use ($product, $validated) {
            $product->update([
                'approval_status' => Product::APPROVAL_REJECTED,
                'approval_note' => $validated['reason'],
                'approved_at' => null,
                'status' => 0,
            ]);

            ProductApproval::create([
                'product_id' => $product->id,
                'action' => 'reject',
                'note' => $validated['reason'],
                'reviewed_by' => auth()->id(),
            ]);

            if ($product->seller_id) {
                User::find($product->seller_id)?->notify(new MarketplaceNotification(
                    'Produk Ditolak',
                    "Produk '{$product->name}' ditolak. Alasan: {$validated['reason']}",
                    route('seller.products.index')
                ));
            }
        });

        return back()->with('success', 'Produk ditolak.');
    }

    public function toggleActive(Product $product): RedirectResponse
    {
        $product->update([
            'status' => (int) $product->status === 1 ? 0 : 1,
        ]);

        return back()->with('success', 'Status aktif produk diperbarui.');
    }
}
