@extends('layouts.dashboard')

@section('title', 'Detail Pesanan — Revolve')
@section('header', 'Pesanan #{{ $order->order_number }}')

@section('content')
@php $sellerItems = $order->items->where('seller_id', auth()->id()); @endphp

<a href="{{ route('seller.orders.index') }}" class="inline-flex items-center gap-2 text-sm text-text-secondary hover:text-text-primary mb-6">
    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
    Kembali ke Pesanan
</a>

@include('partials.alerts')

@if ($order->status === 'paid')
    <div class="card elevation-1 bg-surface p-5 mb-6 flex flex-col sm:flex-row sm:items-center gap-4">
        <div class="flex-grow">
            <p class="font-semibold">Pesanan sudah dibayar — segera proses pesanan ini.</p>
            <p class="text-sm text-text-muted mt-1">Pastikan stok tersedia lalu lanjutkan ke tahap berikutnya.</p>
        </div>
        <form action="{{ route('seller.orders.status', $order) }}" method="POST">
            @csrf
            <input type="hidden" name="status" value="processed">
            <button type="submit" class="btn btn-primary">Mulai Proses Pesanan</button>
        </form>
    </div>
@endif

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
    <div class="xl:col-span-2 space-y-6">
        <div class="card elevation-1 bg-surface p-6">
            <h2 class="form-label mb-4">Status Pesanan</h2>
            <div class="status-stepper">
                @php
                    $steps = [
                        'paid' => 'Dibayar',
                        'processed' => 'Diproses',
                        'packing' => 'Dikemas',
                        'shipped' => 'Dikirim',
                        'delivered' => 'Diterima',
                        'completed' => 'Selesai',
                    ];
                    $currentIndex = array_search($order->status, array_keys($steps));
                    if ($currentIndex === false || $order->status === 'pending' || $order->status === 'waiting_payment') {
                        $currentIndex = -1;
                    }
                @endphp
                @foreach ($steps as $key => $label)
                    @php $stepIndex = array_search($key, array_keys($steps)); @endphp
                    <div class="status-step {{ $currentIndex !== -1 && $stepIndex < $currentIndex ? 'done' : '' }} {{ $stepIndex === $currentIndex ? 'active' : '' }}">
                        <span class="dot"></span>
                        <span class="step-label">{{ $label }}</span>
                    </div>
                @endforeach
            </div>

            @if (in_array($order->status, ['processed', 'packing']))
                <div class="mt-6 p-4 rounded-xl bg-surface-subtle">
                    <p class="text-sm font-semibold mb-3">Perbarui Status</p>
                    <div class="flex flex-wrap gap-2">
                        @if ($order->status === 'processed')
                            <form action="{{ route('seller.orders.status', $order) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="packing">
                                <button type="submit" class="btn btn-outline btn-sm">Tandai Dikemas</button>
                            </form>
                        @endif
                        @if ($order->status === 'packing')
                            <form action="{{ route('seller.orders.status', $order) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="shipped">
                                <button type="submit" class="btn btn-primary btn-sm">Tandai Dikirim</button>
                            </form>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <div class="card elevation-1 bg-surface p-6">
            <h2 class="form-label mb-4">Produk dalam Pesanan (Bagian Anda)</h2>
            <div class="space-y-4">
                @foreach ($sellerItems as $item)
                    <div class="flex gap-4">
                        <div class="w-16 h-16 rounded-lg overflow-hidden bg-surface-subtle shrink-0">
                            @if ($item->product?->primaryImage?->path)
                                <img src="{{ asset('storage/' . $item->product->primaryImage->path) }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-surface-muted"></div>
                            @endif
                        </div>
                        <div class="flex-grow min-w-0">
                            <p class="text-sm font-semibold">{{ $item->product?->name }}</p>
                            @if ($item->variant)
                                <p class="text-xs text-text-muted mt-0.5">
                                    {{ $item->variant->color ? $item->variant->color . ' / ' : '' }}{{ $item->variant->size ? 'Ukuran ' . $item->variant->size : '' }}
                                </p>
                            @endif
                            <p class="text-sm mt-1">Rp {{ number_format((float) $item->price, 0, ',', '.') }} × {{ $item->quantity }}</p>
                            @if ($item->review)
                                <span class="badge badge-primary mt-2">Sudah direview pembeli</span>
                            @endif
                        </div>
                        <p class="text-sm font-semibold shrink-0">Rp {{ number_format((float) $item->subtotal, 0, ',', '.') }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="card elevation-1 bg-surface p-6">
            <h2 class="form-label mb-4">Riwayat Pengiriman</h2>
            @if ($order->tracking->isNotEmpty())
                <div class="space-y-4">
                    @foreach ($order->tracking as $track)
                        <div class="flex gap-3">
                            <div class="flex flex-col items-center">
                                <div class="w-3 h-3 rounded-full bg-primary mt-1"></div>
                                @unless ($loop->last)
                                    <div class="w-px flex-grow bg-border"></div>
                                @endunless
                            </div>
                            <div>
                                <p class="text-sm font-semibold">{{ $track->status }}</p>
                                <p class="text-xs text-text-muted">{{ $track->note }}</p>
                                <p class="text-xs text-text-muted mt-0.5">{{ $track->created_at->translatedFormat('d M Y H:i') }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-text-muted">Belum ada riwayat pengiriman.</p>
            @endif
        </div>
    </div>

    <div class="space-y-6">
        <div class="card elevation-1 bg-surface p-6">
            <h2 class="form-label mb-4">Ringkasan Pesanan</h2>
            <div class="summary-row">
                <span>Subtotal (Bagian Anda)</span>
                <span>Rp {{ number_format((float) $sellerItems->sum('subtotal'), 0, ',', '.') }}</span>
            </div>
            <div class="summary-row">
                <span>Ongkir (Diperkirakan)</span>
                <span>Rp {{ number_format((float) $order->shipping_cost, 0, ',', '.') }}</span>
            </div>
            @if ((float) $order->voucher_discount > 0)
                <div class="summary-row">
                    <span>Diskon Voucher</span>
                    <span class="text-success">- Rp {{ number_format((float) $order->voucher_discount, 0, ',', '.') }}</span>
                </div>
            @endif
            @if ((float) $order->discount > 0)
                <div class="summary-row">
                    <span>Diskon Produk</span>
                    <span class="text-success">- Rp {{ number_format((float) $order->discount, 0, ',', '.') }}</span>
                </div>
            @endif
            <div class="summary-row font-semibold text-price mt-3 pt-3 border-t border-border">
                <span>Total Pesanan</span>
                <span>Rp {{ number_format((float) $order->total, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="card elevation-1 bg-surface p-6">
            <h2 class="form-label mb-4">Info Pembayaran</h2>
            @if ($order->payment)
                <div class="space-y-2 text-sm">
                    <p class="text-text-muted">Metode: <span class="text-text-primary font-semibold">{{ $order->payment->payment_method }}</span></p>
                    @if ($order->payment->bank_name)
                        <p>Bank: {{ $order->payment->bank_name }} {{ $order->payment->bank_account_number }}</p>
                    @endif
                    @if ($order->payment->paid_at)
                        <p>Dibayar: {{ $order->payment->paid_at->translatedFormat('d M Y H:i') }}</p>
                    @endif
                </div>
            @else
                <p class="text-sm text-text-muted">Belum ada pembayaran.</p>
            @endif
        </div>

        <div class="card elevation-1 bg-surface p-6">
            <h2 class="form-label mb-4">Alamat Pengiriman</h2>
            @if ($order->address)
                <p class="text-sm font-semibold">{{ $order->address->receiver_name }}</p>
                <p class="text-sm text-text-secondary mt-1">{{ $order->address->phone }}</p>
                <p class="text-sm text-text-secondary leading-relaxed mt-2">
                    {{ $order->address->full_address }}, {{ $order->address->city }}, {{ $order->address->province }} {{ $order->address->postal_code }}
                </p>
            @else
                <p class="text-sm text-text-muted">Alamat tidak tersedia.</p>
            @endif
        </div>
    </div>
</div>
@endsection