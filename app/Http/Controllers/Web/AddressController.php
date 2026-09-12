<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    public function index()
    {
        $addresses = Address::where('user_id', Auth::id())
            ->orderBy('is_default', 'desc')
            ->paginate(10);

        return view('addresses.index', compact('addresses'));
    }

    public function create()
    {
        return view('addresses.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatedData($request);

        $validated['user_id'] = Auth::id();

        if ($validated['is_default'] ?? false) {
            Address::where('user_id', Auth::id())->update(['is_default' => false]);
        }

        Address::create($validated);

        return redirect()->route('addresses.index')->with('success', 'Alamat ditambahkan');
    }

    public function edit(Address $address)
    {
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        return view('addresses.edit', compact('address'));
    }

    public function update(Request $request, Address $address): RedirectResponse
    {
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $this->validatedData($request, true);

        $validated = array_filter($validated, fn ($v) => $v !== null);

        if (array_key_exists('is_default', $validated) && $validated['is_default']) {
            Address::where('user_id', Auth::id())->update(['is_default' => false]);
        }

        $address->update($validated);

        return redirect()->route('addresses.index')->with('success', 'Alamat diperbarui');
    }

    public function destroy(Address $address): RedirectResponse
    {
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        $address->delete();

        return back()->with('success', 'Alamat dihapus');
    }

    public function setDefault(Address $address): RedirectResponse
    {
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        Address::where('user_id', Auth::id())->update(['is_default' => false]);
        $address->update(['is_default' => true]);

        return back()->with('success', 'Alamat utama diubah');
    }

    private function validatedData(Request $request, bool $partial = false): array
    {
        return $request->validate([
            'receiver_name' => ($partial ? 'sometimes|' : '').'required|string|max:100',
            'phone' => ($partial ? 'sometimes|' : '').'required|string|max:20',
            'province' => ($partial ? 'sometimes|' : '').'required|string|max:100',
            'city' => ($partial ? 'sometimes|' : '').'required|string|max:100',
            'district' => 'nullable|string|max:100',
            'village' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'full_address' => ($partial ? 'sometimes|' : '').'required|string|max:1000',
            'courier_note' => 'nullable|string|max:500',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'is_default' => 'nullable|boolean',
        ]);
    }
}
