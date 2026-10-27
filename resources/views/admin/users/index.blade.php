@extends('layouts.dashboard')

@section('title', 'Pengguna — Revolve')
@section('header', 'Pengguna')

@section('content')
<div class="card elevation-1 bg-surface p-6 lg:p-8">
    <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col sm:flex-row gap-3 mb-6">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / email..." class="form-input sm:flex-grow">
        <select name="role" class="form-input sm:w-44">
            <option value="">Semua Role</option>
            <option value="customer" {{ request('role') == 'customer' ? 'selected' : '' }}>Customer</option>
            <option value="seller" {{ request('role') == 'seller' ? 'selected' : '' }}>Seller</option>
            <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
        </select>
        <button type="submit" class="btn btn-outline">Cari</button>
    </form>

    @include('partials.alerts')

    @if ($users->isNotEmpty())
        <div class="overflow-x-auto">
            <table class="table-auto w-full text-sm">
                <thead>
                    <tr class="text-left text-text-muted border-b border-border">
                        <th class="py-3 pr-4 font-medium">Pengguna</th>
                        <th class="py-3 pr-4 font-medium">Role</th>
                        <th class="py-3 pr-4 font-medium">Status</th>
                        <th class="py-3 pr-4 font-medium">Pesanan</th>
                        <th class="py-3 pr-4 font-medium">Total Belanja</th>
                        <th class="py-3 font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr class="border-b border-border">
                            <td class="py-3 pr-4">
                                <a href="{{ route('admin.users.show', $user) }}" class="flex items-center gap-3">
                                    <span class="w-9 h-9 rounded-full bg-primary/10 text-primary text-xs font-bold flex items-center justify-center shrink-0">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                    <span>
                                        <span class="block font-semibold hover:text-primary">{{ $user->name }}</span>
                                        <span class="block text-xs text-text-muted">{{ $user->email }}</span>
                                    </span>
                                </a>
                            </td>
                            <td class="py-3 pr-4">
                                <span class="badge {{ $user->role === 'admin' ? 'badge-error' : ($user->role === 'seller' ? 'badge-primary' : 'badge-outline') }}">{{ ucfirst($user->role) }}</span>
                            </td>
                            <td class="py-3 pr-4">
                                <span class="badge {{ $user->is_active ? 'badge-success' : 'badge-error' }}">{{ $user->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                            </td>
                            <td class="py-3 pr-4">{{ $user->orders_count }}</td>
                            <td class="py-3 pr-4">Rp {{ number_format((float) $user->order_sum_total, 0, ',', '.') }}</td>
                            <td class="py-3 pr-4">
                                <a href="{{ route('admin.users.show', $user) }}" class="btn btn-outline btn-sm">Detail</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $users->links() }}
        </div>
    @else
        <div class="empty-state"><p>Pengguna tidak ditemukan.</p></div>
    @endif
</div>
@endsection