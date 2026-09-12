<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::with('category')->orderBy('sort_order')->get();

        return view('admin.banners.index', compact('banners'));
    }

    public function create()
    {
        $categories = Category::parents()->with('children')->get();

        return view('admin.banners.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatedData($request);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('banners', 'public');
        }

        Banner::create($validated);

        return redirect()->route('admin.banners.index')->with('success', 'Banner dibuat.');
    }

    public function edit(Banner $banner)
    {
        $categories = Category::parents()->with('children')->get();

        return view('admin.banners.edit', compact('banner', 'categories'));
    }

    public function update(Request $request, Banner $banner): RedirectResponse
    {
        $validated = $this->validatedData($request);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('banners', 'public');
        }

        $banner->update($validated);

        return redirect()->route('admin.banners.index')->with('success', 'Banner diperbarui.');
    }

    public function destroy(Banner $banner): RedirectResponse
    {
        $banner->delete();

        return back()->with('success', 'Banner dihapus.');
    }

    public function toggle(Banner $banner): RedirectResponse
    {
        $banner->update(['is_active' => ! $banner->is_active]);

        return back()->with('success', 'Status banner diperbarui.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'title' => 'required|string|max:150',
            'image' => 'nullable|image|max:2048',
            'link' => 'nullable|string|max:255',
            'type' => 'required|in:promo,category,hero',
            'category_id' => 'nullable|exists:categories,id',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);
    }
}
