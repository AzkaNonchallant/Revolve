@extends('layouts.dashboard')

@section('title', 'Dashboard Seller — Revolve')
@section('header', 'Dashboard Seller')

@section('content')
<div class="space-y-8">
    <div class="stat-grid cols-6">
        <div class="stat-card">
            <div class="stat-icon bg-primary/10 text-primary">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
            </div>
            <p class="stat-value">{{ $totalProducts }}</p>
            <p class="stat-label">Total Produk</p>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-success/10 text-success">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 12 2 2 4-4"/><circle cx="12" cy="12" r="10"/></svg>
            </div>
            <p class="stat-value">{{ $activeProducts }}</p>
            <p class="stat-label">Produk Aktif</p>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-warning/10 text-warning">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 8v4M12 16h.01"/><circle cx="12" cy="12" r="10"/></svg>
            </div>
            <p class="stat-value">{{ $pendingProducts }}</p>
            <p class="stat-label">Menunggu Review</p>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-primary/10 text-primary">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/></svg>
            </div>
            <p class="stat-value">{{ $newOrders }}</p>
            <p class="stat-label">Pesanan Baru</p>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-secondary/10 text-secondary">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 20V10M12 20V4M6 20v-6"/></svg>
            </div>
            <p class="stat-value">{{ $processingOrders }}</p>
            <p class="stat-label">Diproses / Dikemas</p>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-success/10 text-success">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
            </div>
            <p class="stat-value">{{ $completedOrders }}</p>
            <p class="stat-label">Pesanan Selesai</p>
        </div>
    </div>

    <div class="card elevation-1 bg-surface p-6 lg:p-8">
        <h2 class="text-headline-sm mb-1">Ringkasan Keuangan</h2>
        <p class="text-sm text-text-muted mb-6">Total penjualan & pendapatan bersih Anda.</p>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <p class="text-label-sm text-text-muted">Total Penjualan (Kotor)</p>
                <p class="text-price text-2xl mt-1">Rp {{ number_format($totalSales, 0, ',', '.') }}</p>
            </div>
            <div>
                <p class="text-label-sm text-text-muted">Total Komisi Marketplace</p>
                <p class="text-price text-2xl mt-1">Rp {{ number_format($commission, 0, ',', '.') }}</p>
            </div>
            <div>
                <p class="text-label-sm text-text-muted">Pendapatan Bersih</p>
                <p class="text-price text-2xl mt-1 text-success">Rp {{ number_format($revenue, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="card elevation-1 bg-surface p-6 lg:p-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-headline-sm">Produk Terlaris</h2>
                <a href="{{ route('seller.statistics.index') }}" class="text-sm font-semibold text-primary hover:text-primary-hover">Detail →</a>
            </div>
            @if ($bestSellers->isNotEmpty())
                <div class="space-y-4">
                    @foreach ($bestSellers as $product)
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-lg overflow-hidden bg-surface-subtle shrink-0">
                                @if ($product->primaryImage?->image)
                                    <img src="{{ asset('storage/' . $product->primaryImage->image) }}" alt="" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full bg-surface-muted"></div>
                                @endif
                            </div>
                            <div class="flex-grow min-w-0">
                                <p class="text-sm font-semibold truncate">{{ $product->name }}</p>
                                <p class="text-xs text-text-muted">{{ $product->sold_count }} terjual</p>
                            </div>
                            <span class="text-sm font-semibold">Rp {{ number_format((float) $product->activePrice(), 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <p>Belum ada data penjualan.</p>
                </div>
            @endif
        </div>

        <div class="card elevation-1 bg-surface p-6 lg:p-8">
            <h2 class="text-headline-sm mb-6">Aksi Cepat</h2>
            <div class="space-y-3">
                <a href="{{ route('seller.products.create') }}" class="btn btn-primary w-full">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
                    Tambah Produk Baru
                </a>
                <a href="{{ route('seller.products.pending') }}" class="btn btn-outline w-full">Produk Menunggu Review</a>
                <a href="{{ route('seller.orders.index', ['status' => 'paid']) }}" class="btn btn-outline w-full">Proses Pesanan Baru</a>
                <a href="{{ route('seller.sales.index') }}" class="btn btn-outline w-full">Lihat Penjualan</a>
                <a href="{{ route('seller.shop.edit') }}" class="btn btn-outline w-full">Kelola Toko Saya</a>
            </div>
        </div>
    </div>
</div>
@endsection