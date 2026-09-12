<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use App\Notifications\MarketplaceNotification;
use Illuminate\Http\RedirectResponse;

class CommissionController extends Controller
{
    public function index()
    {
        $commissions = Commission::with(['order', 'seller', 'orderItem.product'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.commissions.index', compact('commissions'));
    }

    public function pay(Commission $commission): RedirectResponse
    {
        if ($commission->status === Commission::STATUS_PAID) {
            return back()->with('error', 'Komisi sudah dibayarkan.');
        }

        $commission->update([
            'status' => Commission::STATUS_PAID,
            'paid_at' => now(),
        ]);

        $commission->seller->notify(new MarketplaceNotification(
            'Komisi Dibayarkan',
            'Komisi sebesar Rp'.number_format($commission->commission_amount, 0, ',', '.').' telah dibayarkan.',
            route('seller.sales.index')
        ));

        return back()->with('success', 'Komisi ditandai sudah dibayarkan.');
    }
}
