<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with(['children', 'children.children'])->parents()->withCount('products')->get();

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        $parents = Category::parents()->get();

        return view('admin.categories.create', compact('parents'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatedData($request);

        $data = [
            'parent_id' => $validated['parent_id'] ?? null,
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']).'-'.Str::lower(Str::random(4)),
            'is_active' => $validated['is_active'] ?? true,
        ];

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        Category::create($data);

        return redirect()->route('admin.categories.index')->with('success', 'Kategori dibuat.');
    }

    public function edit(Category $category)
    {
        $parents = Category::parents()->where('id', '!=', $category->id)->get();

        return view('admin.categories.edit', compact('category', 'parents'));
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $this->validatedData($request);

        if ($validated['parent_id'] === $category->id) {
            return back()->with('error', 'Kategori tidak dapat menjadi anak dari dirinya sendiri.');
        }

        $data = [
            'parent_id' => $validated['parent_id'] ?? null,
            'name' => $validated['name'],
            'is_active' => $validated['is_active'] ?? true,
        ];

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        $category->update($data);

        return redirect()->route('admin.categories.index')->with('success', 'Kategori diperbarui.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->products()->exists()) {
            return back()->with('error', 'Kategori masih memiliki produk. Pindahkan produk terlebih dahulu.');
        }

        $category->delete();

        return back()->with('success', 'Kategori dihapus.');
    }

    public function toggle(Category $category): RedirectResponse
    {
        $category->update(['is_active' => ! $category->is_active]);

        return back()->with('success', 'Status kategori diperbarui.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'parent_id' => 'nullable|exists:categories,id',
            'name' => 'required|string|max:100',
            'is_active' => 'nullable|boolean',
            'image' => 'nullable|image|max:2048',
        ]);
    }
}
