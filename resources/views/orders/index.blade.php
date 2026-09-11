@extends('layouts.app')

@section('title', 'Pesanan — Revolve')

@section('content')
<div class="container-app pt-8 lg:pt-12 pb-16">
    <h1 class="text-headline-lg mb-6">Pesanan Saya</h1>

    @include('partials.alerts')

    {{-- Status filter --}}
    <div class="flex flex-wrap gap-2 mb-8">
        <a href="{{ route('orders.index') }}" class="chip {{ !request('status') ? 'chip-active' : '' }}">Semua</a>
        @foreach ([
            'waiting_payment' => 'Menunggu Pembayaran',
            'processed' => 'Diproses',
            'packing' => 'Dikemas',
            'shipped' => 'Dikirim',
            'delivered' => 'Selesai',
            'cancelled' => 'Dibatalkan',
        ] as $value => $label)
            <a href="{{ route('orders.index', ['status' => $value]) }}" class="chip {{ request('status') === $value ? 'chip-active' : '' }}">{{ $label }}</a>
        @endforeach
    </div>

    @if ($orders->isNotEmpty())
        <div class="space-y-5">
            @foreach ($orders as $order)
                <div class="card elevation-1 bg-surface p-5 lg:p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-surface-muted pb-4 mb-4">
                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <a href="{{ route('orders.show', $order) }}" class="text-title-md hover:text-primary transition-colors">{{ $order->order_number }}</a>
                                @include('partials.order-status', ['status' => $order->status])
                            </div>
                            <p class="text-sm text-text-muted mt-1">{{ $order->created_at->translatedFormat('d M Y, H:i') }}</p>
                        </div>
                        <div class="text-left sm:text-right">
                            <p class="text-price">Rp {{ number_format((float) $order->total, 0, ',', '.') }}</p>
                            <p class="text-xs text-text-muted mt-1 capitalize">{{ str_replace('_', ' ', $order->payment_method ?? '—') }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="flex -space-x-3">
                            @foreach ($order->items->take(4) as $item)
                                <div class="w-12 h-12 rounded-lg overflow-hidden bg-surface-subtle ring-2 ring-surface">
                                    @if ($item->product->image)
                                        <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            <svg class="w-4 h-4 text-text-muted/40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 2L2 7l10 5 10-5-10-5z"/></svg>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        <p class="text-sm text-text-secondary">
                            {{ $order->items->count() }} produk
                            @if ($order->items->count() > 4)
                                <span class="text-text-muted">+{{ $order->items->count() - 4 }} lainnya</span>
                            @endif
                        </p>
                        <div class="flex-grow"></div>
                        <a href="{{ route('orders.show', $order) }}" class="btn btn-outline btn-sm">Detail</a>
                    </div>
                </div>
            @endforeach
        </div>

        @if ($orders->hasPages())
            <div class="mt-10">
                {{ $orders->links() }}
            </div>
        @endif
    @else
        <div class="empty-state card elevation-1 bg-surface">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/>
                <path d="M3 6h18"/>
                <path d="M16 10a4 4 0 0 1-8 0"/>
            </svg>
            <h3 class="text-title-md mb-2">Belum ada pesanan</h3>
            <p>
                @if(request('status'))
                    Tidak ada pesanan dengan status ini.
                @else
                    Anda belum memiliki pesanan apapun.
                @endif
            </p>
            <a href="{{ route('products.index') }}" class="btn btn-primary">Mulai Belanja</a>
        </div>
    @endif
</div>
@endsection