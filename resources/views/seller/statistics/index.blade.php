@extends('layouts.dashboard')

@section('title', 'Statistik — Revolve')
@section('header', 'Statistik Penjualan')

@section('content')
@php
    $dayLabels = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
    $weekLabels = array_map(fn ($k) => 'Minggu ' . $k, [4, 3, 2, 1]);
    $monthLabels = array_map(fn ($k) => now()->subMonths($k)->translatedFormat('M'), [5, 4, 3, 2, 1]);
@endphp

<div class="space-y-6">
    <div class="card elevation-1 bg-surface p-6 lg:p-8">
        <h2 class="form-label mb-1">Penjualan 7 Hari Terakhir</h2>
        <p class="text-xs text-text-muted mb-6">Dalam rupiah (Rp)</p>
        @php $maxDaily = $daily->max() ?: 1; @endphp
        <div class="flex items-end gap-2 h-40">
            @foreach ($daily as $date => $amount)
                <div class="flex-1 flex flex-col items-center gap-2 h-full justify-end">
                    <span class="text-[10px] text-text-muted">{{ $amount > 0 ? number_format($amount / 1000, 0) . 'k' : '' }}</span>
                    <div class="w-full rounded-t-lg bg-primary/80 hover:bg-primary" style="height: {{ round($amount / $maxDaily * 100) }}%"></div>
                    <span class="text-[10px] text-text-muted">{{ \Carbon\Carbon::parse($date)->translatedFormat('D') }}</span>
                </div>
            @endforeach
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="card elevation-1 bg-surface p-6 lg:p-8">
            <h2 class="form-label mb-1">Penjualan Bulanan</h2>
            <p class="text-xs text-text-muted mb-6">6 bulan terakhir (Rp)</p>
            @php $maxMonthly = max($monthly->max(), 1); @endphp
            <div class="flex items-end gap-2 h-40">
                @foreach ($monthly as $key => $amount)
                    <div class="flex-1 flex flex-col items-center gap-2 h-full justify-end">
                        <span class="text-[10px] text-text-muted">{{ $amount > 0 ? number_format($amount / 1000, 0) . 'k' : '' }}</span>
                        <div class="w-full rounded-t-lg bg-secondary/70 hover:bg-secondary" style="height: {{ round($amount / $maxMonthly * 100) }}%"></div>
                        <span class="text-[10px] text-text-muted">{{ \Carbon\Carbon::parse($key . '-01')->translatedFormat('M') }}</span>
                    </div>
                @endforeach
            </div>
        </div>

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
                <div class="empty-state"><p>Belum ada data penjualan.</p></div>
            @endif
        </div>
    </div>

    <div class="card elevation-1 bg-surface p-6 lg:p-8">
        <h2 class="form-label mb-6">Kategori Terlaris</h2>
        @if ($bestCategories->isNotEmpty())
            @php $maxCat = max($bestCategories->max(), 1); @endphp
            <div class="space-y-3">
                @foreach ($bestCategories as $name => $count)
                    <div class="flex items-center gap-3">
                        <span class="text-sm w-40 truncate">{{ $name }}</span>
                        <div class="flex-grow h-3 rounded-full bg-surface-subtle overflow-hidden">
                            <div class="h-full rounded-full bg-primary" style="width: {{ round($count / $maxCat * 100) }}%"></div>
                        </div>
                        <span class="text-xs text-text-muted w-10 text-right">{{ $count }}</span>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state"><p>Belum ada data penjualan.</p></div>
        @endif
    </div>
</div>
@endsection