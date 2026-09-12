@extends('layouts.dashboard')

@section('title', 'Penjualan — Revolve')
@section('header', 'Penjualan')

@section('content')
<div class="stat-grid cols-3 mb-8">
    <div class="stat-card">
        <p class="stat-value">{{ number_format($monthly, 0, ',', '.') }}</p>
        <p class="stat-label">Penjualan Bulan Ini (Rp)</p>
    </div>
    <div class="stat-card">
        <p class="stat-value">{{ number_format($totalSales, 0, ',', '.') }}</p>
        <p class="stat-label">Total Penjualan (Kotor, Rp)</p>
    </div>
    <div class="stat-card">
        <p class="stat-value text-success">{{ number_format($totalNet, 0, ',', '.') }}</p>
        <p class="stat-label">Pendapatan Bersih (Rp)</p>
    </div>
</div>

<div class="card elevation-1 bg-surface p-6 lg:p-8">
    <h2 class="form-label mb-4">Ringkasan Komisi</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-8">
        <div class="p-5 rounded-xl bg-surface-subtle">
            <p class="text-sm text-text-muted">Total Komisi Marketplace</p>
            <p class="text-price text-2xl mt-1">Rp {{ number_format($totalCommissions, 0, ',', '.') }}</p>
            <p class="text-xs text-text-muted mt-2">Komisi dipotong dari setiap transaksi berhasil.</p>
        </div>
        <div class="p-5 rounded-xl bg-surface-subtle">
            <p class="text-sm text-text-muted">Pendapatan Bersih (Setelah Komisi)</p>
            <p class="text-price text-2xl mt-1 text-success">Rp {{ number_format($totalNet, 0, ',', '.') }}</p>
        </div>
    </div>

    <h2 class="form-label mb-4">Rincian Komisi Transaksi</h2>
    @if ($commissions->isNotEmpty())
        <div class="overflow-x-auto">
            <table class="table-auto w-full text-sm">
                <thead>
                    <tr class="text-left text-text-muted border-b border-border">
                        <th class="py-3 pr-4 font-medium">Pesanan</th>
                        <th class="py-3 pr-4 font-medium">Produk</th>
                        <th class="py-3 pr-4 font-medium">Subtotal Item</th>
                        <th class="py-3 pr-4 font-medium">Komisi</th>
                        <th class="py-3 font-medium">Bersih</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($commissions as $commission)
                        <tr class="border-b border-border">
                            <td class="py-3 pr-4">
                                @if ($commission->order)
                                    <a href="{{ route('seller.orders.show', $commission->order) }}" class="font-semibold text-primary hover:text-primary-hover">#{{ $commission->order->order_number }}</a>
                                @else
                                    <span class="text-text-muted">—</span>
                                @endif
                            </td>
                            <td class="py-3 pr-4 truncate max-w-[12rem]">{{ $commission->orderItem?->product?->name ?? '—' }}</td>
                            <td class="py-3 pr-4">Rp {{ number_format((float) $commission->subtotal, 0, ',', '.') }}</td>
                            <td class="py-3 pr-4 text-warning">- Rp {{ number_format((float) $commission->commission_amount, 0, ',', '.') }}</td>
                            <td class="py-3 font-semibold text-success">Rp {{ number_format((float) $commission->net_amount, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $commissions->links() }}
        </div>
    @else
        <div class="empty-state">
            <p>Belum ada komisi dari transaksi.</p>
        </div>
    @endif
</div>
@endsection