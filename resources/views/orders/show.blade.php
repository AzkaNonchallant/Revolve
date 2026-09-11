@extends('layouts.app')

@section('title', 'Pesanan ' . $order->order_number . ' — Revolve')

@section('content')
<div class="container-app pt-8 lg:pt-12 pb-16">
    <a href="{{ route('orders.index') }}" class="inline-flex items-center gap-2 text-sm text-text-secondary hover:text-text-primary mb-6">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        Kembali ke Pesanan
    </a>

    @include('partials.alerts')

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center gap-3 flex-wrap">
                <h1 class="text-headline-lg">{{ $order->order_number }}</h1>
                @include('partials.order-status', ['status' => $order->status])
            </div>
            <p class="text-text-secondary text-sm mt-1">
                Dibuat {{ $order->created_at->translatedFormat('l, d M Y H:i') }}
            </p>
        </div>
        @if (in_array($order->status, ['waiting_payment', 'processed']))
            <form action="{{ route('orders.cancel', $order) }}" method="POST" onsubmit="return confirm('Batalkan pesanan ini?')">
                @csrf
                <button type="submit" class="btn btn-outline text-error border-error/40 hover:border-error">Batalkan Pesanan</button>
            </form>
        @endif
    </div>

    {{-- Tracking timeline --}}
    @if ($order->tracking->isNotEmpty())
        <div class="card elevation-1 bg-surface p-6 lg:p-8 mb-8">
            <h2 class="text-headline-sm mb-6">Status Pengiriman</h2>
            <ol class="relative border-l border-surface-muted ml-2 space-y-8">
                @foreach ($order->tracking->sortByDesc('created_at') as $track)
                    <li class="ml-6">
                        <span class="absolute w-4 h-4 rounded-full bg-primary -left-2 mt-1 {{ $loop->first ? 'ring-4 ring-primary/20' : 'bg-primary/30' }}"></span>
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                            <p class="text-sm font-semibold">
                                {{ ['waiting_payment' => 'Menunggu Pembayaran', 'processed' => 'Diproses', 'packing' => 'Dikemas', 'shipped' => 'Dikirim', 'delivered' => 'Selesai', 'cancelled' => 'Dibatalkan'][$track->status] ?? ucwords(str_replace('_', ' ', $track->status)) }}
                            </p>
                            <span class="text-xs text-text-muted">{{ $track->created_at->translatedFormat('d M Y, H:i') }}</span>
                        </div>
                        @if ($track->note)
                            <p class="text-sm text-text-secondary mt-1">{{ $track->note }}</p>
                        @endif
                    </li>
                @endforeach
            </ol>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
        <div class="lg:col-span-2 space-y-8">
            {{-- Items --}}
            <div class="card elevation-1 bg-surface p-6 lg:p-8">
                <h2 class="text-headline-sm mb-6">Produk Dipesan</h2>
                <div class="divide-y divide-surface-muted">
                    @foreach ($order->items as $item)
                        <div class="py-4 first:pt-0 last:pb-0 flex items-center gap-4">
                            <a href="{{ route('products.show', $item->product) }}" class="w-16 h-16 rounded-lg overflow-hidden bg-surface-subtle shrink-0">
                                @if ($item->product->image)
                                    <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-text-muted/40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 2L2 7l10 5 10-5-10-5z"/></svg>
                                    </div>
                                @endif
                            </a>
                            <div class="flex-grow min-w-0">
                                <a href="{{ route('products.show', $item->product) }}" class="text-sm font-semibold hover:text-primary transition-colors line-clamp-1">{{ $item->product->name }}</a>
                                <p class="text-xs text-text-muted mt-0.5">Rp {{ number_format((float) $item->price, 0, ',', '.') }} × {{ $item->quantity }}</p>
                            </div>
                            <span class="text-sm font-semibold whitespace-nowrap">Rp {{ number_format((float) $item->subtotal, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Address --}}
            <div class="card elevation-1 bg-surface p-6 lg:p-8">
                <h2 class="text-headline-sm mb-4">Alamat Pengiriman</h2>
                <p class="text-title-md">{{ $order->address->receiver_name }}</p>
                <p class="text-sm text-text-secondary mt-1">{{ $order->address->phone }}</p>
                <p class="text-sm text-text-secondary mt-2 leading-relaxed">
                    {{ $order->address->full_address }}, {{ $order->address->village ? $order->address->village . ', ' : '' }}{{ $order->address->district ? $order->address->district . ', ' : '' }}{{ $order->address->city }}, {{ $order->address->province }}{{ $order->address->postal_code ? ' ' . $order->address->postal_code : '' }}
                </p>
            </div>

            {{-- Payment --}}
            @if ($order->payment)
                <div class="card elevation-1 bg-surface p-6 lg:p-8">
                    <h2 class="text-headline-sm mb-4">Informasi Pembayaran</h2>
                    <dl class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-text-secondary">Metode</dt>
                            <dd class="font-semibold capitalize">{{ str_replace('_', ' ', $order->payment->payment_method) }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-text-secondary">Jumlah</dt>
                            <dd class="font-semibold">Rp {{ number_format((float) $order->payment->amount, 0, ',', '.') }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-text-secondary">Status</dt>
                            <dd>
                                <span class="badge {{ $order->payment->payment_status === 'paid' ? 'badge-success' : ($order->payment->payment_status === 'failed' ? 'badge-error' : 'badge-warning') }}">
                                    {{ ['pending' => 'Menunggu', 'paid' => 'Lunas', 'failed' => 'Gagal'][$order->payment->payment_status] ?? $order->payment->payment_status }}
                                </span>
                            </dd>
                        </div>
                        @if ($order->payment->paid_at)
                            <div class="flex justify-between">
                                <dt class="text-text-secondary">Dibayar</dt>
                                <dd class="font-semibold">{{ $order->payment->paid_at->translatedFormat('d M Y, H:i') }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>
            @endif
        </div>

        {{-- Summary --}}
        <div class="lg:sticky lg:top-24 h-fit space-y-6">
            <div class="card elevation-1 bg-surface p-6 lg:p-8">
                <h2 class="text-headline-sm mb-6">Ringkasan</h2>
                <dl class="space-y-3 text-sm mb-6">
                    <div class="flex justify-between">
                        <dt class="text-text-secondary">Subtotal</dt>
                        <dd class="font-semibold">Rp {{ number_format((float) $order->subtotal, 0, ',', '.') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-text-secondary">Ongkos kirim</dt>
                        <dd class="font-semibold">Rp {{ number_format((float) $order->shipping_cost, 0, ',', '.') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-text-secondary">Diskon</dt>
                        <dd class="font-semibold">Rp {{ number_format((float) $order->discount, 0, ',', '.') }}</dd>
                    </div>
                    <div class="divider"></div>
                    <div class="flex justify-between items-center">
                        <dt class="text-title-md">Total</dt>
                        <dd class="text-price text-2xl">Rp {{ number_format((float) $order->total, 0, ',', '.') }}</dd>
                    </div>
                </dl>
                @if ($order->delivery_schedule)
                    <div class="flex items-center gap-2 p-3 rounded-lg bg-surface-subtle text-sm">
                        <svg class="w-4 h-4 text-text-muted shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                        <span class="text-text-secondary">Jadwal: <span class="font-semibold text-text-primary">{{ $order->delivery_schedule->translatedFormat('d M Y H:i') }}</span></span>
                    </div>
                @endif
                @if ($order->courier)
                    <div class="flex items-center gap-2 p-3 rounded-lg bg-surface-subtle text-sm mt-2">
                        <svg class="w-4 h-4 text-text-muted shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 3h15v13H1z"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                        <span class="text-text-secondary">{{ $order->courier->name }} — {{ $order->courier->service }}</span>
                    </div>
                @endif
                @if ($order->shippingRate)
                    <div class="flex items-center gap-2 p-3 rounded-lg bg-surface-subtle text-sm mt-2">
                        <svg class="w-4 h-4 text-text-muted shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                        <span class="text-text-secondary">{{ $order->shippingRate->city ? $order->shippingRate->city . ', ' : '' }}{{ $order->shippingRate->province }}{{ $order->shippingRate->etd_days ? ' • ' . $order->shippingRate->etd_days . ' hari' : '' }}</span>
                    </div>
                @endif
            </div>
            <a href="{{ route('products.index') }}" class="btn btn-outline w-full">Lanjut Belanja</a>
        </div>
    </div>
</div>
@endsection