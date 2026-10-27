<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SellerRequest;
use App\Models\User;
use App\Notifications\MarketplaceNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SellerRequestController extends Controller
{
    public function create()
    {
        $user = Auth::user();

        if ($user->isSeller()) {
            return redirect()->route('seller.dashboard');
        }

        $latest = $user->latestSellerRequest;

        $categories = Category::parents()->get();

        return view('seller-request.create', compact('latest', 'categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if ($user->isSeller()) {
            return back()->with('error', 'Akun Anda sudah menjadi Seller.');
        }

        $hasPending = $user->sellerRequests()
            ->where('status', SellerRequest::STATUS_PENDING)
            ->exists();

        if ($hasPending) {
            return back()->with('error', 'Anda sudah memiliki pengajuan yang menunggu review.');
        }

        $validated = $request->validate([
            'shop_name' => 'required|string|max:120',
            'shop_description' => 'nullable|string|max:1000',
            'phone' => 'required|string|max:30',
            'address' => 'required|string|max:1000',
            'category' => 'nullable|string|max:120',
            'reason' => 'required|string|max:1000',
            'logo' => 'nullable|image|max:2048',
        ]);

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('shops', 'public');
        }

        $sellerRequest = $user->sellerRequests()->create([
            'shop_name' => $validated['shop_name'],
            'shop_description' => $validated['shop_description'] ?? null,
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'category' => $validated['category'] ?? null,
            'reason' => $validated['reason'],
            'logo' => $logoPath,
            'status' => SellerRequest::STATUS_PENDING,
        ]);

        foreach (User::where('role', User::ROLE_ADMIN)->get() as $admin) {
            $admin->notify(new MarketplaceNotification(
                'Permintaan Seller Baru',
                "{$user->name} mengajukan menjadi Seller (toko '{$sellerRequest->shop_name}').",
                route('admin.seller-requests.show', $sellerRequest)
            ));
        }

        return redirect()->route('seller-request.index')->with('success', 'Pengajuan Seller berhasil dikirim. Menunggu review Admin.');
    }

    public function index()
    {
        $requests = Auth::user()->sellerRequests()->latest()->paginate(10);

        return view('seller-request.index', compact('requests'));
    }
}
