@extends('layouts.dashboard')

@section('title', 'Detail Seller — Revolve')
@section('header', 'Seller: {{ $seller->name }}')

@section('content')
@include('partials.alerts')

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="space-y-6">
        <div class="card elevation-1 bg-surface p-6 text-center">
            @if ($seller->shop?->logo)
                <img src="{{ asset('storage/' . $seller->shop->logo) }}" class="w-20 h-20 rounded-2xl object-cover mx-auto" alt="">
            @else
                <span class="w-20 h-20 rounded-2xl bg-primary/10 text-primary text-3xl font-bold flex items-center justify-center mx-auto">{{ strtoupper(substr($seller->shop?->name ?? $seller->name, 0, 1)) }}</span>
            @endif
            <h2 class="text-headline-sm mt-4">{{ $seller->shop?->name ?? 'Belum ada toko' }}</h2>
            <p class="text-sm text-text-muted">{{ $seller->name }} · {{ $seller->email }}</p>
            <div class="flex items-center justify-center gap-2 mt-3 flex-wrap">
                <span class="badge badge-primary">Seller</span>
                <span class="badge {{ $seller->is_active ? 'badge-success' : 'badge-error' }}">{{ $seller->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                @if ($seller->shop?->is_verified)
                    <span class="badge badge-success">Terverifikasi</span>
                @endif
            </div>
            @if ($seller->shop?->description)
                <p class="text-xs text-text-secondary mt-3 text-justify">{{ $seller->shop->description }}</p>
            @endif

            @if ($seller->shop)
                <form action="{{ route('admin.sellers.suspend', $seller) }}" method="POST" class="mt-4">
                    @csrf
                    <button type="submit" class="btn {{ $seller->shop->is_active ? 'btn-outline text-error' : 'btn-primary' }} w-full">
                        {{ $seller->shop->is_active ? 'Nonaktifkan Seller' : 'Aktifkan Seller' }}
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="lg:col-span-2 space-y-6">
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
            @foreach ($stats as $label => $value)
                <div class="card elevation-1 bg-surface p-5 text-center">
                    <p class="stat-value">{{ is_numeric($value) ? number_format((float) $value, 0, ',', '.') : $value }}</p>
                    <p class="stat-label">{{ $label }}</p>
                </div>
            @endforeach
        </div>

        <div class="card elevation-1 bg-surface p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="form-label mb-0">Produk Terakhir</h2>
                <a href="{{ route('admin.products.index', ['search' => $seller->name]) }}" class="text-sm font-semibold text-primary">Lihat Semua →</a>
            </div>
            @if ($seller->products->isNotEmpty())
                <div class="space-y-3">
                    @foreach ($seller->products as $product)
                        <a href="{{ route('admin.products.show', $product) }}" class="flex items-center gap-3 p-3 rounded-xl border border-border hover:border-primary transition-colors">
                            <div class="w-12 h-12 rounded-lg overflow-hidden bg-surface-subtle shrink-0">
                                @if ($product->primaryImage?->path)
                                    <img src="{{ asset('storage/' . $product->primaryImage->path) }}" alt="" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full bg-surface-muted"></div>
                                @endif
                            </div>
                            <div class="flex-grow min-w-0">
                                <p class="text-sm font-semibold truncate">{{ $product->name }}</p>
                                <p class="text-xs text-text-muted">Rp {{ number_format($product->activePrice(), 0, ',', '.') }}</p>
                            </div>
                            <span class="badge {{ $product->approval_status === 'approved' ? 'badge-success' : ($product->approval_status === 'pending' ? 'badge-warning' : 'badge-error') }}">{{ $product->approvalStatusLabel() }}</span>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="empty-state"><p>Belum ada produk.</p></div>
            @endif
        </div>
    </div>
</div>
@endsection