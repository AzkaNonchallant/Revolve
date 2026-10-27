@extends('layouts.dashboard')

@section('title', 'Detail Pesanan — Revolve')
@section('header', 'Pesanan #{{ $order->order_number }}')

@section('content')
@include('partials.alerts')

<a href="{{ route('admin.orders.index') }}" class="inline-flex items-center gap-2 text-sm text-text-secondary hover:text-text-primary mb-6">
    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
    Kembali ke Pesanan
</a>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
    <div class="xl:col-span-2 space-y-6">
        <div class="card elevation-1 bg-surface p-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-headline-sm">Status: <span class="badge badge-primary">{{ $order->statusLabel() }}</span></h2>
                    <p class="text-xs text-text-muted mt-1">Dibuat {{ $order->created_at->translatedFormat('d M Y H:i') }}</p>
                </div>
                @if (in_array($order->status, ['paid', 'processed', 'packing', 'shipped', 'delivered']))
                    <form action="{{ route('admin.orders.status', $order) }}" method="POST">
                        @csrf
                        <input type="hidden" name="status" value="completed">
                        <button type="submit" class="btn btn-primary">Tandai Selesai</button>
                    </form>
                @endif
            </div>

            @if (in_array($order->status, ['pending', 'waiting_payment', 'paid']))
                <div class="p-4 rounded-xl bg-surface-subtle">
                    <p class="text-sm font-semibold mb-3">Aksi Admin</p>
                    <div class="flex flex-wrap gap-2">
                        @if (in_array($order->status, ['pending', 'waiting_payment']))
                            <form action="{{ route('admin.orders.status', $order) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="cancelled">
                                <button type="submit" class="btn btn-outline text-error btn-sm" onclick="return confirm('Batalkan pesanan ini?')">Batalkan Pesanan</button>
                            </form>
                        @endif
                        @if ($order->status === 'paid')
                            <form action="{{ route('admin.orders.status', $order) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="shipped">
                                <button type="submit" class="btn btn-outline btn-sm">Tandai Dikirim</button>
                            </form>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <div class="card elevation-1 bg-surface p-6">
            <h2 class="form-label mb-4">Produk dalam Pesanan</h2>
            <div class="space-y-4">
                @foreach ($order->items as $item)
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
                            <p class="text-xs text-text-muted mt-1">Seller: {{ $item->seller?->shop?->name ?? $item->seller?->name ?? '—' }}</p>
                            <p class="text-sm mt-1">Rp {{ number_format((float) $item->price, 0, ',', '.') }} × {{ $item->quantity }}</p>
                        </div>
                        <p class="text-sm font-semibold shrink-0">Rp {{ number_format((float) $item->subtotal, 0, ',', '.') }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="card elevation-1 bg-surface p-6">
            <h2 class="form-label mb-4">Riwayat Status</h2>
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
                                <p class="text-sm font-semibold">{{ $order::STATUS_MAP[$track->status] ?? ucwords(str_replace('_', ' ', $track->status)) }}</p>
                                <p class="text-xs text-text-muted">{{ $track->note }}</p>
                                <p class="text-xs text-text-muted mt-0.5">{{ $track->created_at->translatedFormat('d M Y H:i') }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-text-muted">Belum ada riwayat status.</p>
            @endif
        </div>
    </div>

    <div class="space-y-6">
        <div class="card elevation-1 bg-surface p-6">
            <h2 class="form-label mb-4">Ringkasan</h2>
            <div class="summary-row"><span>Subtotal</span><span>Rp {{ number_format((float) $order->subtotal, 0, ',', '.') }}</span></div>
            <div class="summary-row"><span>Ongkir</span><span>Rp {{ number_format((float) $order->shipping_cost, 0, ',', '.') }}</span></div>
            @if ((float) $order->voucher_discount > 0)
                <div class="summary-row"><span>Diskon Voucher</span><span class="text-success">- Rp {{ number_format((float) $order->voucher_discount, 0, ',', '.') }}</span></div>
            @endif
            @if ((float) $order->discount > 0)
                <div class="summary-row"><span>Diskon Produk</span><span class="text-success">- Rp {{ number_format((float) $order->discount, 0, ',', '.') }}</span></div>
            @endif
            <div class="summary-row font-semibold text-price mt-3 pt-3 border-t border-border">
                <span>Total</span><span>Rp {{ number_format((float) $order->total, 0, ',', '.') }}</span>
            </div>
            @if ($order->voucher)
                <div class="mt-3 pt-3 border-t border-border text-xs text-text-muted">
                    <p>Voucher: <span class="badge badge-outline">{{ $order->voucher->code }}</span></p>
                </div>
            @endif
        </div>

        <div class="card elevation-1 bg-surface p-6">
            <h2 class="form-label mb-4">Pembeli</h2>
            <p class="text-sm font-semibold">{{ $order->user?->name ?? '—' }}</p>
            <p class="text-sm text-text-secondary">{{ $order->user?->email }}</p>
            @if ($order->user)
                <a href="{{ route('admin.users.show', $order->user) }}" class="btn btn-outline btn-sm mt-3">Lihat Profil</a>
            @endif
        </div>

        <div class="card elevation-1 bg-surface p-6">
            <h2 class="form-label mb-4">Pembayaran</h2>
            @if ($order->payment)
                <div class="space-y-2 text-sm">
                    <p class="text-text-muted">Status: <span class="badge badge-outline">{{ $order->payment->statusLabel() }}</span></p>
                    <p>Metode: {{ $order->payment->payment_method }}</p>
                    @if ($order->payment->bank_name)
                        <p>Bank: {{ $order->payment->bank_name }} {{ $order->payment->bank_account_number }}</p>
                    @endif
                    <p class="text-xs text-text-muted">Transaksi: {{ $order->payment->transaction_id }}</p>
                    @if ($order->payment->due_date)
                        <p class="text-xs text-text-muted">Jatuh tempo: {{ $order->payment->due_date->translatedFormat('d M Y H:i') }}</p>
                    @endif
                    @if ($order->payment->paid_at)
                        <p class="text-xs text-text-muted">Dibayar: {{ $order->payment->paid_at->translatedFormat('d M Y H:i') }}</p>
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
            @if ($order->courier)
                <p class="text-xs text-text-muted mt-3">Ekspedisi: {{ $order->courier->name }}</p>
            @endif
        </div>
    </div>
</div>
@endsection