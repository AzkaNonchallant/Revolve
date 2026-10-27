@extends('layouts.dashboard')

@section('title', 'Pesanan Penjualan — Revolve')
@section('header', 'Pesanan Penjualan')

@section('content')
<div class="card elevation-1 bg-surface p-6 lg:p-8">
    <form method="GET" action="{{ route('seller.orders.index') }}" class="flex flex-col sm:flex-row gap-3 mb-6">
        <select name="status" class="form-input sm:w-56">
            <option value="">Semua Status</option>
            @foreach (\App\Models\Order::STATUS_MAP as $value => $label)
                <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-outline">Filter</button>
        @if (request('status') !== null)
            <a href="{{ route('seller.orders.index') }}" class="btn btn-outline btn-sm self-center">Reset</a>
        @endif
    </form>

    @include('partials.alerts')

    @if ($orders->isNotEmpty())
        <div class="space-y-4">
            @foreach ($orders as $order)
                @php $sellerItems = $order->items->where('seller_id', auth()->id()); @endphp
                <a href="{{ route('seller.orders.show', $order) }}" class="block card bg-surface-secondary border border-border rounded-xl p-4 hover:border-primary transition-colors">
                    <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                        <div class="flex-grow min-w-0">
                            <div class="flex items-center gap-3 flex-wrap">
                                <p class="font-semibold">#{{ $order->order_number }}</p>
                                <span class="badge {{ $order->status === 'paid' ? 'badge-primary' : 'badge-outline' }}">{{ $order->statusLabel() }}</span>
                            </div>
                            <p class="text-xs text-text-muted mt-1">
                                {{ $order->created_at->translatedFormat('d M Y H:i') }} · {{ $order->user?->name }}
                            </p>
                            <p class="text-sm mt-2">
                                {{ $sellerItems->count() }} produk · Total bagian Anda:
                                <span class="text-price">Rp {{ number_format((float) $sellerItems->sum('subtotal'), 0, ',', '.') }}</span>
                            </p>
                        </div>
                        @if ($order->status === 'paid')
                            <a href="{{ route('seller.orders.show', $order) }}" class="btn btn-primary btn-sm shrink-0">Proses Pesanan</a>
                        @else
                            <span class="text-primary text-xs font-semibold shrink-0">Lihat Detail →</span>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $orders->links() }}
        </div>
    @else
        <div class="empty-state">
            <p>Belum ada pesanan.</p>
        </div>
    @endif
</div>
@endsection