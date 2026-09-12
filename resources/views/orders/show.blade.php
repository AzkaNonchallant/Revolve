@extends('layouts.app')

@section('title', 'Pesanan ' . $order->order_number . ' — Revolve')

@php
    $stepStatuses = ['waiting_payment', 'paid', 'processed', 'packing', 'shipped', 'delivered', 'completed'];
    $currentIndex = array_search($order->status, $stepStatuses);
    $cancelled = in_array($order->status, ['cancelled', 'refunded', 'returned']);
    $stepLabels = ['Menunggu Pembayaran', 'Dibayar', 'Diproses', 'Dikemas', 'Dikirim', 'Diterima', 'Selesai'];
@endphp

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
        <div class="flex items-center gap-2 flex-wrap">
            @if ($order->isPayable())
                <form action="{{ route('orders.pay', $order) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary">
                        Bayar Sekarang
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2"/><path d="M1 10h22"/></svg>
                    </button>
                </form>
            @endif
            @if ($order->isCancellable())
                <form action="{{ route('orders.cancel', $order) }}" method="POST" onsubmit="return confirm('Batalkan pesanan ini?')">
                    @csrf
                    <button type="submit" class="btn btn-outline text-error border-error/40 hover:border-error">Batalkan Pesanan</button>
                </form>
            @endif
            @if (in_array($order->status, ['shipped', 'delivered']))
                <form action="{{ route('orders.complete', $order) }}" method="POST" onsubmit="return confirm('Konfirmasi pesanan sudah diterima dan selesai?')">
                    @csrf
                    <button type="submit" class="btn btn-primary">Konfirmasi Selesai</button>
                </form>
            @endif
        </div>
    </div>

    {{-- Status stepper --}}
    @if (! $cancelled)
        <div class="card elevation-1 bg-surface p-6 lg:p-8 mb-8">
            <div class="status-stepper">
                @foreach ($stepLabels as $i => $label)
                    <div class="status-step {{ $currentIndex !== false && $i < $currentIndex ? 'done' : '' }} {{ $currentIndex === $i ? 'active' : '' }}">
                        <span class="dot"></span>
                        <span class="step-label">{{ $label }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Tracking timeline --}}
    @if ($order->tracking->isNotEmpty())
        <div class="card elevation-1 bg-surface p-6 lg:p-8 mb-8">
            <h2 class="text-headline-sm mb-6">Riwayat Status</h2>
            <ol class="relative border-l border-surface-muted ml-2 space-y-8">
                @foreach ($order->tracking->sortByDesc('created_at') as $track)
                    <li class="ml-6">
                        <span class="absolute w-4 h-4 rounded-full bg-primary -left-2 mt-1 {{ $loop->first ? 'ring-4 ring-primary/20' : 'bg-primary/30' }}"></span>
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                            <p class="text-sm font-semibold">
                                {{ $order::STATUS_MAP[$track->status] ?? ucwords(str_replace('_', ' ', $track->status)) }}
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
                        <div class="py-4 first:pt-0 last:pb-0">
                            <div class="flex items-center gap-4">
                                <a href="{{ route('products.show', $item->product) }}" class="w-16 h-16 rounded-lg overflow-hidden bg-surface-subtle shrink-0">
                                    @if ($item->product?->primaryImage?->image ?? $item->product?->image)
                                        <img src="{{ asset('storage/' . ($item->product->primaryImage?->image ?? $item->product->image)) }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            <svg class="w-5 h-5 text-text-muted/40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 2L2 7l10 5 10-5-10-5z"/></svg>
                                        </div>
                                    @endif
                                </a>
                                <div class="flex-grow min-w-0">
                                    <a href="{{ route('products.show', $item->product) }}" class="text-sm font-semibold hover:text-primary transition-colors line-clamp-1">{{ $item->product->name }}</a>
                                    @if ($item->variant)
                                        <p class="text-xs text-text-muted mt-0.5">Varian: {{ $item->variant->label }}</p>
                                    @endif
                                    <p class="text-xs text-text-muted mt-0.5">Rp {{ number_format((float) $item->price, 0, ',', '.') }} × {{ $item->quantity }}</p>
                                    @if ($item->discount > 0)
                                        <p class="text-xs text-success mt-0.5">Diskon voucher: −Rp {{ number_format((float) $item->discount, 0, ',', '.') }}</p>
                                    @endif
                                    @if ($item->seller?->shop)
                                        <a href="{{ route('shops.show', $item->seller->shop) }}" class="text-xs text-primary hover:text-primary-hover mt-0.5 inline-block">{{ $item->seller->shop->name }}</a>
                                    @endif
                                </div>
                                <span class="text-sm font-semibold whitespace-nowrap">Rp {{ number_format((float) $item->subtotal, 0, ',', '.') }}</span>
                            </div>
                            @if ($order->status === 'completed' && ! $item->review)
                                <div class="mt-4 p-4 bg-surface-subtle rounded-xl">
                                    <form action="{{ route('orders.review.store', [$order, $item]) }}" method="POST">
                                        @csrf
                                        <p class="text-sm font-semibold mb-2">Tulis ulasan untuk produk ini</p>
                                        <div class="flex items-center gap-1 mb-3" data-star-rating>
                                            @for ($i = 1; $i <= 5; $i++)
                                                <button type="button" data-star value="{{ $i }}" class="text-text-muted hover:text-[#f5b301] transition-colors" aria-label="{{ $i }} bintang">
                                                    <svg class="w-7 h-7" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="1"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                                </button>
                                            @endfor
                                        </div>
                                        <input type="hidden" name="rating" value="5">
                                        <textarea name="comment" rows="2" class="form-input text-sm!" placeholder="Bagaimana kualitas produknya?"></textarea>
                                        <button type="submit" class="btn btn-primary btn-sm mt-2">Kirim Ulasan</button>
                                    </form>
                                </div>
                            @elseif ($item->review)
                                <div class="mt-3 flex items-start gap-3 p-3 bg-surface-subtle rounded-xl">
                                    <span class="stars sm mt-0.5">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <svg viewBox="0 0 24 24" fill="{{ $i <= $item->review->rating ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2" class="{{ $i > $item->review->rating ? 'muted' : '' }}"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                        @endfor
                                    </span>
                                    <div class="flex-grow">
                                        <p class="text-xs text-text-secondary">{{ $item->review->comment ?: 'Tidak ada komentar.' }}</p>
                                        <p class="text-xs text-text-muted mt-1">Ulasan Anda · {{ $item->review->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Address --}}
            @if ($order->address)
                <div class="card elevation-1 bg-surface p-6 lg:p-8">
                    <h2 class="text-headline-sm mb-4">Alamat Pengiriman</h2>
                    <p class="text-title-md">{{ $order->address->receiver_name }}</p>
                    <p class="text-sm text-text-secondary mt-1">{{ $order->address->phone }}</p>
                    <p class="text-sm text-text-secondary mt-2 leading-relaxed">
                        {{ $order->address->full_address }}, {{ $order->address->village ? $order->address->village . ', ' : '' }}{{ $order->address->district ? $order->address->district . ', ' : '' }}{{ $order->address->city }}, {{ $order->address->province }}{{ $order->address->postal_code ? ' ' . $order->address->postal_code : '' }}
                    </p>
                </div>
            @endif

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
                            <dt class="text-text-secondary">Transaksi</dt>
                            <dd class="font-semibold">{{ $order->payment->transaction_id }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-text-secondary">Jumlah</dt>
                            <dd class="font-semibold">Rp {{ number_format((float) $order->payment->amount, 0, ',', '.') }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-text-secondary">Status</dt>
                            <dd>
                                <span class="badge {{ $order->payment->payment_status === 'paid' ? 'badge-success' : ($order->payment->payment_status === 'failed' ? 'badge-error' : 'badge-warning') }}">
                                    {{ ['pending' => 'Menunggu', 'paid' => 'Lunas', 'failed' => 'Gagal', 'refunded' => 'Refund'][$order->payment->payment_status] ?? $order->payment->payment_status }}
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
                    @if ($order->isPayable())
                        <div class="mt-4 p-4 bg-primary/5 border border-primary/30 rounded-xl text-sm">
                            <p class="text-text-secondary">Pembayaran simulasi dapat diselesaikan dengan menekan tombol <span class="font-semibold text-primary">Bayar Sekarang</span> di atas.</p>
                        </div>
                    @endif
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
                    @if ($order->voucher_discount > 0)
                        <div class="flex justify-between">
                            <dt class="text-text-secondary">Voucher <span class="badge badge-primary ml-1">{{ $order->voucher_code }}</span></dt>
                            <dd class="font-semibold text-success">−Rp {{ number_format((float) $order->voucher_discount, 0, ',', '.') }}</dd>
                        </div>
                    @endif
                    <div class="flex justify-between">
                        <dt class="text-text-secondary">Ongkos kirim</dt>
                        <dd class="font-semibold">Rp {{ number_format((float) $order->shipping_cost, 0, ',', '.') }}</dd>
                    </div>
                    <div class="divider"></div>
                    <div class="flex justify-between items-center">
                        <dt class="text-title-md">Total</dt>
                        <dd class="text-price text-2xl">Rp {{ number_format((float) $order->total, 0, ',', '.') }}</dd>
                    </div>
                </dl>
                @if ($order->voucher)
                    <div class="flex items-center gap-2 p-3 rounded-lg bg-surface-subtle text-sm mb-2">
                        <svg class="w-4 h-4 text-text-muted shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 9a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v1a2 2 0 0 0 0 4v1a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2v-1a2 2 0 0 0 0-4z"/><path d="M14 10v4"/></svg>
                        <span class="text-text-secondary">Voucher <span class="font-semibold text-primary">{{ $order->voucher->code }}</span> · {{ $order->voucher->name }}</span>
                    </div>
                @endif
                @if ($order->courier)
                    <div class="flex items-center gap-2 p-3 rounded-lg bg-surface-subtle text-sm">
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-star-rating]').forEach((group) => {
        const input = group.parentElement.querySelector('input[name="rating"]');
        const stars = group.querySelectorAll('[data-star]');
        stars.forEach((star) => {
            star.addEventListener('click', () => {
                const value = parseInt(star.dataset.value, 10);
                input.value = value;
                stars.forEach((s) => {
                    const on = parseInt(s.dataset.value, 10) <= value;
                    s.classList.toggle('text-[#f5b301]', on);
                    s.classList.toggle('text-text-muted', !on);
                });
            });
        });
    });
});
</script>
@endpush