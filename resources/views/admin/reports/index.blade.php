@extends('layouts.dashboard')

@section('title', 'Laporan — Revolve')
@section('header', 'Laporan Marketplace')

@section('content')
<div class="card elevation-1 bg-surface p-6 lg:p-8 mb-6">
    <form method="GET" action="{{ route('admin.reports.index') }}" class="flex items-center gap-3">
        <label class="form-label mb-0">Periode</label>
        <select name="period" class="form-input sm:w-44" onchange="this.form.submit()">
            <option value="daily" {{ $period == 'daily' ? 'selected' : '' }}>30 Hari Terakhir</option>
            <option value="weekly" {{ $period == 'weekly' ? 'selected' : '' }}>4 Minggu Terakhir</option>
            <option value="monthly" {{ $period == 'monthly' ? 'selected' : '' }}>6 Bulan Terakhir</option>
            <option value="yearly" {{ $period == 'yearly' ? 'selected' : '' }}>1 Tahun Terakhir</option>
        </select>
        <noscript><button type="submit" class="btn btn-outline">Terapkan</button></noscript>
    </form>
</div>

<div class="stat-grid cols-3 mb-8">
    <div class="stat-card">
        <p class="stat-value">{{ number_format($totalTransaksi, 0, ',', '.') }}</p>
        <p class="stat-label">Total Transaksi</p>
    </div>
    <div class="stat-card">
        <p class="stat-value">Rp {{ number_format($totalOmzet, 0, ',', '.') }}</p>
        <p class="stat-label">Total Omzet</p>
    </div>
    <div class="stat-card">
        <p class="stat-value">Rp {{ number_format($totalKomisi, 0, ',', '.') }}</p>
        <p class="stat-label">Total Komisi</p>
    </div>
</div>

<div class="card elevation-1 bg-surface p-6 lg:p-8 mb-6">
    <h2 class="form-label mb-6">Grafik Penjualan Harian</h2>
    @php $chartData = $chartData->keyBy('day'); @endphp
    @php $maxChart = max($chartData->max('total_sales'), 1); @endphp
    <div class="flex items-end gap-1 h-40 overflow-x-auto pb-2">
        @for ($i = 29; $i >= 0; $i--)
            @php $day = now()->subDays($i)->toDateString(); @endphp
            @php $amount = (int) ($chartData[$day]->total_sales ?? 0); @endphp
            <div class="min-w-[14px] flex-1 flex flex-col items-center gap-1 h-full justify-end">
                <div class="w-full rounded-t bg-primary/70" style="height: {{ round($amount / $maxChart * 100) }}%"></div>
            </div>
        @endfor
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="card elevation-1 bg-surface p-6 lg:p-8">
        <h2 class="form-label mb-6">Produk Terlaris</h2>
        @if ($topProducts->isNotEmpty())
            <div class="space-y-4">
                @foreach ($topProducts as $product)
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
                @foreach ($topSellers as $row)
                    @php $seller = $topSellersUser[$row->seller_id] ?? null; @endphp
                    <div class="flex items-center gap-3">
                        <span class="w-10 h-10 rounded-full bg-primary/10 text-primary font-bold flex items-center justify-center shrink-0">{{ strtoupper(substr($seller?->shop?->name ?? '?', 0, 1)) }}</span>
                        <div class="flex-grow min-w-0">
                            <p class="text-sm font-semibold truncate">{{ $seller?->shop?->name ?? 'Seller #' . $row->seller_id }}</p>
                            <p class="text-xs text-text-muted">{{ $seller?->name }}</p>
                        </div>
                        <span class="text-sm font-semibold">Rp {{ number_format((int) $row->total_sales, 0, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state"><p>Belum ada data.</p></div>
        @endif
    </div>
</div>

<div class="card elevation-1 bg-surface p-6 lg:p-8 mt-6">
    <h2 class="form-label mb-6">Kategori Terlaris</h2>
    @if ($topCategories->isNotEmpty())
        @php $maxCat = max($topCategories->max(), 1); @endphp
        <div class="space-y-3">
            @foreach ($topCategories as $name => $count)
                <div class="flex items-center gap-3">
                    <span class="text-sm w-44 truncate">{{ $name }}</span>
                    <div class="flex-grow h-3 rounded-full bg-surface-subtle overflow-hidden">
                        <div class="h-full rounded-full bg-primary" style="width: {{ round($count / $maxCat * 100) }}%"></div>
                    </div>
                    <span class="text-xs text-text-muted w-10 text-right">{{ $count }}</span>
                </div>
            @endforeach
        </div>
    @else
        <div class="empty-state"><p>Belum ada data.</p></div>
    @endif
</div>
@endsection