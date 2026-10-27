@extends('layouts.dashboard')

@section('title', 'Detail Pengguna — Revolve')
@section('header', 'Pengguna: {{ $user->name }}')

@section('content')
@include('partials.alerts')

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="space-y-6">
        <div class="card elevation-1 bg-surface p-6 text-center">
            <span class="w-20 h-20 rounded-full bg-primary/10 text-primary text-3xl font-bold flex items-center justify-center mx-auto">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
            <h2 class="text-headline-sm mt-4">{{ $user->name }}</h2>
            <p class="text-sm text-text-muted">{{ $user->email }}</p>
            <div class="flex items-center justify-center gap-2 mt-3">
                <span class="badge {{ $user->role === 'admin' ? 'badge-error' : ($user->role === 'seller' ? 'badge-primary' : 'badge-outline') }}">{{ ucfirst($user->role) }}</span>
                <span class="badge {{ $user->is_active ? 'badge-success' : 'badge-error' }}">{{ $user->is_active ? 'Aktif' : 'Nonaktif' }}</span>
            </div>
            @if ($user->phone_number)
                <p class="text-sm text-text-secondary mt-3">{{ $user->phone_number }}</p>
            @endif
            <p class="text-xs text-text-muted mt-1">Bergabung {{ $user->created_at->translatedFormat('d M Y') }}</p>

            @unless ($user->id === auth()->id())
                <form action="{{ route('admin.users.toggle-active', $user) }}" method="POST" class="mt-4">
                    @csrf
                    <button type="submit" class="btn {{ $user->is_active ? 'btn-outline text-error' : 'btn-primary' }} w-full">
                        {{ $user->is_active ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}
                    </button>
                </form>
            @endunless
        </div>
    </div>

    <div class="lg:col-span-2 space-y-6">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach ($statCards as $label => $value)
                <div class="card elevation-1 bg-surface p-5 text-center">
                    <p class="stat-value">{{ is_numeric($value) ? number_format((float) $value, 0, ',', '.') : $value }}</p>
                    <p class="stat-label">{{ $label }}</p>
                </div>
            @endforeach
        </div>

        <div class="card elevation-1 bg-surface p-6">
            <h2 class="form-label mb-4">Pesanan Terakhir</h2>
            @if ($user->orders->isNotEmpty())
                <div class="space-y-3">
                    @foreach ($user->orders as $order)
                        <a href="{{ route('admin.orders.show', $order) }}" class="flex items-center justify-between gap-4 p-4 rounded-xl border border-border hover:border-primary transition-colors">
                            <div>
                                <p class="text-sm font-semibold">#{{ $order->order_number }}</p>
                                <p class="text-xs text-text-muted mt-1">{{ $order->created_at->translatedFormat('d M Y H:i') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold">Rp {{ number_format((float) $order->total, 0, ',', '.') }}</p>
                                <span class="badge badge-outline mt-1">{{ $order->statusLabel() }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="empty-state"><p>Belum ada pesanan.</p></div>
            @endif
        </div>
    </div>
</div>
@endsection