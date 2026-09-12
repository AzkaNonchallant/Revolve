<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    public function index()
    {
        $vouchers = Voucher::with(['category', 'seller'])->latest()->paginate(12);

        return view('admin.vouchers.index', compact('vouchers'));
    }

    public function create()
    {
        $categories = Category::parents()->with('children')->get();
        $sellers = User::where('role', User::ROLE_SELLER)->whereHas('shop')->get();

        return view('admin.vouchers.create', compact('categories', 'sellers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatedData($request);

        $validated['code'] = strtoupper(trim($validated['code']));

        Voucher::create($validated);

        return redirect()->route('admin.vouchers.index')->with('success', 'Voucher dibuat.');
    }

    public function edit(Voucher $voucher)
    {
        $categories = Category::parents()->with('children')->get();
        $sellers = User::where('role', User::ROLE_SELLER)->whereHas('shop')->get();

        return view('admin.vouchers.edit', compact('voucher', 'categories', 'sellers'));
    }

    public function update(Request $request, Voucher $voucher): RedirectResponse
    {
        $validated = $this->validatedData($request);
        $validated['code'] = strtoupper(trim($validated['code']));

        $voucher->update($validated);

        return redirect()->route('admin.vouchers.index')->with('success', 'Voucher diperbarui.');
    }

    public function destroy(Voucher $voucher): RedirectResponse
    {
        $voucher->delete();

        return back()->with('success', 'Voucher dihapus.');
    }

    public function toggle(Voucher $voucher): RedirectResponse
    {
        $voucher->update(['is_active' => ! $voucher->is_active]);

        return back()->with('success', 'Status voucher diperbarui.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'code' => 'required|string|max:50|unique:vouchers,code,'.($request->route('voucher')?->id),
            'name' => 'required|string|max:120',
            'type' => 'required|in:percent,nominal',
            'value' => 'required|numeric|min:0',
            'min_purchase' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'quota' => 'nullable|integer|min:0',
            'scope' => 'required|in:marketplace,category,seller',
            'category_id' => 'nullable|exists:categories,id',
            'seller_id' => 'nullable|exists:users,id',
            'is_active' => 'nullable|boolean',
        ]);
    }
}
