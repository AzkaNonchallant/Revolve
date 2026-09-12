@extends('layouts.dashboard')

@section('title', 'Dashboard Admin — Revolve')
@section('header', 'Dashboard Admin')

@section('content')
<div class="space-y-8">
    <div class="stat-grid cols-4 mb-4">
        <div class="stat-card">
            <p class="stat-value">{{ $totalUsers }}</p>
            <p class="stat-label">Total Pengguna</p>
        </div>
        <div class="stat-card">
            <p class="stat-value">{{ $totalSellers }}</p>
            <p class="stat-label">Seller</p>
        </div>
        <div class="stat-card">
            <p class="stat-value">{{ $totalProducts }}</p>
            <p class="stat-label">Total Produk</p>
        </div>
        <div class="stat-card">
            <p class="stat-value">{{ $totalOrders }}</p>
            <p class="stat-label">Total Pesanan</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="card elevation-1 bg-surface p-5 flex items-center gap-4">
            <div class="stat-icon bg-warning/10 text-warning">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 8v4M12 16h.01"/><circle cx="12" cy="12" r="10"/></svg>
            </div>
            <div class="flex-grow">
                <p class="font-semibold">{{ $pendingSellerRequests }}</p>
                <p class="text-xs text-text-muted">Pengajuan Seller Menunggu</p>
            </div>
            <a href="{{ route('admin.seller-requests.index', ['status' => 'pending']) }}" class="btn btn-outline btn-sm">Proses</a>
        </div>
        <div class="card elevation-1 bg-surface p-5 flex items-center gap-4">
            <div class="stat-icon bg-warning/10 text-warning">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 8v4M12 16h.01"/><circle cx="12" cy="12" r="10"/></svg>
            </div>
            <div class="flex-grow">
                <p class="font-semibold">{{ $pendingProducts }}</p>
                <p class="text-xs text-text-muted">Produk Menunggu Review</p>
            </div>
            <a href="{{ route('admin.products.index', ['approval' => 'pending']) }}" class="btn btn-outline btn-sm">Review</a>
        </div>
        <div class="card elevation-1 bg-surface p-5 flex items-center gap-4">
            <div class="stat-icon bg-warning/10 text-warning">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 8v4M12 16h.01"/><circle cx="12" cy="12" r="10"/></svg>
            </div>
            <div class="flex-grow">
                <p class="font-semibold">{{ $pendingOrders }}</p>
                <p class="text-xs text-text-muted">Pesanan Belum Dibayar</p>
            </div>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-outline btn-sm">Lihat</a>
        </div>
    </div>

    <div class="card elevation-1 bg-surface p-6 lg:p-8">
        <h2 class="text-headline-sm mb-1">Ringkasan Keuangan</h2>
        <p class="text-xs text-text-muted mb-6">Transaksi dengan status aktif (dibayar s/d selesai).</p>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <p class="text-label-sm text-text-muted">Total Transaksi (Kotor)</p>
                <p class="text-price text-2xl mt-1">Rp {{ number_format($totalTransactions, 0, ',', '.') }}</p>
            </div>
            <div>
                <p class="text-label-sm text-text-muted">Total Komisi (Terutang)</p>
                <p class="text-price text-2xl mt-1">Rp {{ number_format($totalCommission, 0, ',', '.') }}</p>
            </div>
            <div>
                <p class="text-label-sm text-text-muted">Pendapatan Platform (Komisi Dibayar)</p>
                <p class="text-price text-2xl mt-1 text-success">Rp {{ number_format($platformRevenue, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="card elevation-1 bg-surface p-6 lg:p-8">
            <h2 class="form-label mb-6">Produk Terlaris</h2>
            @if ($bestSellers->isNotEmpty())
                <div class="space-y-4">
                    @foreach ($bestSellers as $product)
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-lg overflow-hidden bg-surface-subtle shrink-0">
                                @if ($product->primaryImage?->path)
                                    <img src="{{ asset('storage/' . $product->primaryImage->path) }}" alt="" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full bg-surface-muted"></div>
                                @endif
                            </div>
                            <div class="flex-grow min-w-0">
                                <p class="text-sm font-semibold truncate">{{ $product->name }}</p>
                                <p class="text-xs text-text-muted">{{ $product->sold_count }} terjual</p>
                            </div>
                            <span class="text-sm font-semibold">Rp {{ number_format($product->activePrice(), 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state"><p>Belum ada data.</p></div>
            @endif
        </div>

        <div class="card elevation-1 bg-surface p-6 lg:p-8">
            <h2 class="form-label mb-6">Top Seller</h2>
            @if ($topSellers->isNotEmpty())
                <div class="space-y-4">
                    @foreach ($topSellers as $seller)
                        <div class="flex items-center gap-3">
                            <span class="w-10 h-10 rounded-full bg-primary/10 text-primary font-bold flex items-center justify-center shrink-0">
                                {{ strtoupper(substr($seller->shop?->name ?? $seller->name, 0, 1)) }}
                            </span>
                            <div class="flex-grow min-w-0">
                                <p class="text-sm font-semibold truncate">{{ $seller->shop?->name ?? $seller->name }}</p>
                                <p class="text-xs text-text-muted">{{ $seller->name }}</p>
                            </div>
                            <span class="text-sm font-semibold">Rp {{ number_format($seller->revenue, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state"><p>Belum ada penjualan.</p></div>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="card elevation-1 bg-surface p-6 lg:p-8">
            <h2 class="form-label mb-6">Penjualan 7 Hari Terakhir</h2>
            @php $maxDaily = max($daily->max(), 1); @endphp
            <div class="flex items-end gap-2 h-40">
                @foreach ($daily as $date => $amount)
                    <div class="flex-1 flex flex-col items-center gap-2 h-full justify-end">
                        <span class="text-[10px] text-text-muted">{{ $amount > 0 ? number_format($amount / 1000, 0) . 'k' : '' }}</span>
                        <div class="w-full rounded-t-lg bg-primary/80" style="height: {{ round($amount / $maxDaily * 100) }}%"></div>
                        <span class="text-[10px] text-text-muted">{{ $date }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="card elevation-1 bg-surface p-6 lg:p-8">
            <h2 class="form-label mb-6">Penjualan Bulanan</h2>
            @php $maxMonthly = max($monthly->max(), 1); @endphp
            <div class="flex items-end gap-2 h-40">
                @foreach ($monthly as $month => $amount)
                    <div class="flex-1 flex flex-col items-center gap-2 h-full justify-end">
                        <span class="text-[10px] text-text-muted">{{ $amount > 0 ? number_format($amount / 1000, 0) . 'k' : '' }}</span>
                        <div class="w-full rounded-t-lg bg-secondary/70" style="height: {{ round($amount / $maxMonthly * 100) }}%"></div>
                        <span class="text-[10px] text-text-muted">{{ $month }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection