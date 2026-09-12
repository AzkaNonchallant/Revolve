<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserChangeRequest;
use App\Notifications\MarketplaceNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserChangeRequestController extends Controller
{
    public function index()
    {
        $requests = Auth::user()->changeRequests()->latest()->paginate(15);

        return view('change-requests.index', compact('requests'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'field' => 'required|in:name,email,phone_number',
            'new_value' => 'required|string|max:255',
            'reason' => 'required|string|max:1000',
        ]);

        $user = Auth::user();

        $existing = $user->changeRequests()
            ->where('field', $validated['field'])
            ->where('status', UserChangeRequest::STATUS_PENDING)
            ->exists();

        if ($existing) {
            return back()->with('error', 'Anda sudah memiliki pengajuan perubahan yang sama dan sedang menunggu review.');
        }

        $oldValue = $user->{$validated['field']} ?? null;

        if ($validated['field'] === 'email') {
            $request->validate(['new_value' => 'email|unique:users,email,'.$user->id]);
        }

        if ($validated['new_value'] === $oldValue) {
            return back()->with('error', 'Nilai baru sama dengan nilai saat ini.');
        }

        $changeRequest = $user->changeRequests()->create([
            'field' => $validated['field'],
            'old_value' => $oldValue,
            'new_value' => $validated['new_value'],
            'reason' => $validated['reason'],
            'status' => UserChangeRequest::STATUS_PENDING,
        ]);

        foreach (User::where('role', User::ROLE_ADMIN)->get() as $admin) {
            $admin->notify(new MarketplaceNotification(
                'Permintaan Perubahan Data',
                "{$user->name} mengajukan perubahan {$changeRequest->field_label}.",
                route('admin.change-requests.show', $changeRequest)
            ));
        }

        return back()->with('success', 'Permintaan perubahan data dikirim. Menunggu persetujuan Admin.');
    }
}
