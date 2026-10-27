<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserChangeRequest;
use App\Notifications\MarketplaceNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserChangeRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = UserChangeRequest::with('user')
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->latest();

        $requests = $query->paginate(15)->withQueryString();

        return view('admin.change-requests.index', compact('requests'));
    }

    public function show(UserChangeRequest $changeRequest)
    {
        return view('admin.change-requests.show', ['changeRequest' => $changeRequest->load('user')]);
    }

    public function approve(UserChangeRequest $changeRequest): RedirectResponse
    {
        if ($changeRequest->status !== UserChangeRequest::STATUS_PENDING) {
            return back()->with('error', 'Request sudah diproses.');
        }

        if ($changeRequest->field === 'email') {
            $exists = User::where('email', $changeRequest->new_value)
                ->where('id', '!=', $changeRequest->user_id)
                ->exists();

            if ($exists) {
                return back()->with('error', 'Email sudah digunakan user lain. Persetujuan tidak dapat dilakukan.');
            }
        }

        DB::transaction(function () use ($changeRequest) {
            $changeRequest->user->update([$changeRequest->field => $changeRequest->new_value]);

            $changeRequest->update([
                'status' => UserChangeRequest::STATUS_APPROVED,
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
            ]);

            $changeRequest->user->notify(new MarketplaceNotification(
                'Permintaan Perubahan Data Disetujui',
                "Perubahan {$changeRequest->field_label} Anda telah disetujui.",
                route('profile.edit')
            ));
        });

        return redirect()->route('admin.change-requests.index')->with('success', 'Perubahan data disetujui dan diterapkan.');
    }

    public function reject(Request $request, UserChangeRequest $changeRequest): RedirectResponse
    {
        if ($changeRequest->status !== UserChangeRequest::STATUS_PENDING) {
            return back()->with('error', 'Request sudah diproses.');
        }

        $validated = $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        $changeRequest->update([
            'status' => UserChangeRequest::STATUS_REJECTED,
            'admin_note' => $validated['reason'],
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        $changeRequest->user->notify(new MarketplaceNotification(
            'Permintaan Perubahan Data Ditolak',
            "Perubahan {$changeRequest->field_label} ditolak. Alasan: {$validated['reason']}",
            route('change-requests.index')
        ));

        return redirect()->route('admin.change-requests.index')->with('success', 'Permintaan perubahan ditolak.');
    }
}
