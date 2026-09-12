<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::withCount(['orders'])
            ->withSum(['orders as order_sum_total' => fn ($q) => $q->whereIn('status', ['paid', 'processed', 'packing', 'shipped', 'delivered', 'completed'])], 'total')
            ->when($request->filled('search'), fn ($q) => $q->where(function ($inner) use ($request) {
                $inner->where('name', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%");
            }))
            ->when($request->filled('role'), fn ($q) => $q->where('role', $request->role))
            ->latest();

        $users = $query->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        $user->loadCount(['orders']);
        $user->load(['orders' => fn ($q) => $q->latest()->limit(10)]);

        $statCards = [
            'Pesanan' => $user->orders()->count(),
            'Total Belanja' => (int) $user->orders()->whereIn('status', ['paid', 'processed', 'packing', 'shipped', 'delivered', 'completed'])->sum('total'),
            'Pesanan Aktif' => $user->orders()->whereIn('status', ['paid', 'processed', 'packing', 'shipped'])->count(),
            'Review Diberikan' => $user->reviews()->count(),
        ];

        return view('admin.users.show', compact('user', 'statCards'));
    }

    public function toggleActive(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun sendiri.');
        }

        $user->update([
            'is_active' => ! $user->is_active,
            'suspended_at' => $user->is_active ? null : now(),
        ]);

        $action = $user->is_active ? 'diaktifkan kembali' : 'dinonaktifkan';

        return back()->with('success', "Akun {$user->name} telah {$action}.");
    }
}
