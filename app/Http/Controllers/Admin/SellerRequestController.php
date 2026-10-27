<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SellerRequest;
use App\Models\Shop;
use App\Models\User;
use App\Notifications\MarketplaceNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SellerRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = SellerRequest::with('user')
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->latest();

        $requests = $query->paginate(15)->withQueryString();

        return view('admin.seller-requests.index', compact('requests'));
    }

    public function show(SellerRequest $sellerRequest)
    {
        return view('admin.seller-requests.show', ['sellerRequest' => $sellerRequest->load('user')]);
    }

    public function approve(SellerRequest $sellerRequest): RedirectResponse
    {
        if ($sellerRequest->status !== SellerRequest::STATUS_PENDING) {
            return back()->with('error', 'Request sudah pernah diproses.');
        }

        $user = $sellerRequest->user;

        DB::transaction(function () use ($sellerRequest, $user) {
            $user->update([
                'role' => User::ROLE_SELLER,
                'phone_number' => $user->phone_number ?: $sellerRequest->phone,
            ]);

            Shop::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'name' => $sellerRequest->shop_name,
                    'slug' => $this->makeShopSlug($sellerRequest->shop_name),
                    'description' => $sellerRequest->shop_description,
                    'phone' => $sellerRequest->phone,
                    'address' => $sellerRequest->address,
                    'logo' => $sellerRequest->logo,
                    'is_active' => true,
                    'is_verified' => true,
                ]
            );

            $sellerRequest->update([
                'status' => SellerRequest::STATUS_APPROVED,
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
            ]);
        });

        $user->notify(new MarketplaceNotification(
            'Selamat, Anda Menjadi Seller!',
            "Pengajuan Seller untuk toko '{$sellerRequest->shop_name}' disetujui.",
            route('seller.dashboard')
        ));

        return redirect()->route('admin.seller-requests.index')->with('success', 'Pengajuan Seller disetujui. Role user diperbarui menjadi Seller.');
    }

    public function reject(Request $request, SellerRequest $sellerRequest): RedirectResponse
    {
        if ($sellerRequest->status !== SellerRequest::STATUS_PENDING) {
            return back()->with('error', 'Request sudah pernah diproses.');
        }

        $validated = $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        $sellerRequest->update([
            'status' => SellerRequest::STATUS_REJECTED,
            'admin_note' => $validated['reason'],
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        $sellerRequest->user->notify(new MarketplaceNotification(
            'Pengajuan Seller Ditolak',
            "Pengajuan Seller Anda ditolak. Alasan: {$validated['reason']}",
            route('seller-request.index')
        ));

        return redirect()->route('admin.seller-requests.index')->with('success', 'Pengajuan Seller ditolak.');
    }

    private function makeShopSlug(string $name): string
    {
        $slug = Str::slug($name);
        $base = $slug;

        while (Shop::where('slug', $slug)->exists()) {
            $slug = $base.'-'.Str::lower(Str::random(4));
        }

        return $slug;
    }
}
