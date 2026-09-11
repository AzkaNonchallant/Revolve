@extends('layouts.app')

@section('title', 'Checkout — Revolve')

@section('content')
<div class="container-app pt-8 lg:pt-12 pb-16">
    <h1 class="text-headline-lg mb-8">Checkout</h1>

    <form action="{{ route('checkout.store') }}" method="POST" id="checkout-form">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
            <div class="lg:col-span-2 space-y-8">

                <div class="card elevation-1 bg-surface p-6 lg:p-8">
                    <h2 class="text-headline-sm mb-6 flex items-center justify-between gap-4">
                        <span>Alamat Pengiriman</span>
                        <a href="{{ route('addresses.create') }}" class="text-sm font-semibold text-primary hover:text-primary-hover inline-flex items-center gap-1">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
                            Tambah Alamat
                        </a>
                    </h2>
                    @if ($addresses->isNotEmpty())
                        <div class="space-y-3">
                            @foreach ($addresses as $address)
                                <label class="flex items-start gap-3 p-4 rounded-xl border border-border cursor-pointer hover:border-border-hover transition-colors {{ (old('address_id') == $address->id || (!old('address_id') && $loop->first)) ? 'border-obsidian bg-surface-subtle' : '' }}" data-address-card>
                                    <input type="radio" name="address_id" value="{{ $address->id }}" class="form-radio mt-1"
                                        @checked(old('address_id', $addresses->where('is_default', true)->first()?->id ?? $addresses->first()->id) == $address->id)>
                                    <span class="flex-grow">
                                        <span class="flex items-center gap-2 flex-wrap">
                                            <span class="text-title-md">{{ $address->receiver_name }}</span>
                                            @if ($address->is_default)
                                                <span class="badge badge-outline">Utama</span>
                                            @endif
                                        </span>
                                        <span class="block text-sm text-text-secondary mt-1">{{ $address->phone }}</span>
                                        <span class="block text-sm text-text-secondary mt-1 leading-relaxed">
                                            {{ $address->full_address }}, {{ $address->village ? $address->village . ', ' : '' }}{{ $address->district ? $address->district . ', ' : '' }}{{ $address->city }}, {{ $address->province }}{{ $address->postal_code ? ' ' . $address->postal_code : '' }}
                                        </span>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/>
                                <circle cx="12" cy="10" r="3"/>
                            </svg>
                            <p>Belum ada alamat tersimpan.</p>
                            <a href="{{ route('addresses.create') }}" class="btn btn-primary btn-sm">Tambah Alamat</a>
                        </div>
                    @endif
                    @error('address_id')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="card elevation-1 bg-surface p-6 lg:p-8">
                    <h2 class="text-headline-sm mb-2">Metode Pengiriman</h2>
                    <p class="text-sm text-text-secondary mb-6">Berat total: {{ number_format($totalWeight, 0, ',', '.') }} g ({{ ceil($totalWeight / 1000) }} kg)</p>

                    @if ($couriers->isNotEmpty())
                        <div class="space-y-4">
                            <input type="hidden" name="courier_id" value="{{ old('courier_id') }}" data-courier-input>
                            @foreach ($couriers as $courier)
                                <div>
                                    <p class="text-label-lg text-text-secondary mb-2">{{ $courier->name }} — {{ $courier->service }}</p>
                                    @if ($courier->shippingRates->isNotEmpty())
                                        <div class="space-y-2">
                                            @foreach ($courier->shippingRates as $rate)
                                                @php
                                                    $estimatedCost = (float) $rate->rate_per_kg * max(ceil($totalWeight / 1000), 1);
                                                @endphp
                                                <label class="flex items-center justify-between gap-3 p-4 rounded-xl border border-border cursor-pointer hover:border-border-hover transition-colors" data-shipping-option>
                                                    <span class="flex items-center gap-3 flex-grow">
                                                        <input type="radio" name="shipping_rate_id" value="{{ $rate->id }}"
                                                            data-courier="{{ $courier->id }}" data-cost="{{ $estimatedCost }}"
                                                            class="form-radio"
                                                            @checked((old('shipping_rate_id') == $rate->id) || (!$loop->parent->first && $loop->first && !old('shipping_rate_id')))>
                                                        <span>
                                                            <span class="block text-sm font-semibold">
                                                                {{ $rate->city ? $rate->city . ', ' : '' }}{{ $rate->province }}
                                                            </span>
                                                            <span class="block text-xs text-text-muted mt-0.5">
                                                                Estimasi {{ $rate->etd_days ? $rate->etd_days . ' hari' : '-' }}
                                                            </span>
                                                        </span>
                                                    </span>
                                                    <span class="text-sm font-semibold whitespace-nowrap">
                                                        Rp {{ number_format($estimatedCost, 0, ',', '.') }}
                                                    </span>
                                                </label>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-sm text-text-muted">Belum ada tarif untuk kurir ini.</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-text-muted">Belum ada kurir tersedia.</p>
                    @endif
                    @error('shipping_rate_id')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="card elevation-1 bg-surface p-6 lg:p-8">
                    <h2 class="text-headline-sm mb-6">Metode Pembayaran</h2>
                    @php
                        $paymentMethods = [
                            'virtual_account' => ['label' => 'Virtual Account', 'icon' => 'bank'],
                            'bank_transfer' => ['label' => 'Transfer Bank', 'icon' => 'landmark'],
                            'e_wallet' => ['label' => 'E-Wallet', 'icon' => 'smartphone'],
                            'cod' => ['label' => 'COD (Bayar di Tempat)', 'icon' => 'dollar'],
                        ];
                    @endphp
                    <div class="space-y-2">
                        @foreach ($paymentMethods as $value => $method)
                            <label class="flex items-center gap-3 p-4 rounded-xl border border-border cursor-pointer hover:border-border-hover transition-colors {{ old('payment_method') == $value ? 'border-obsidian bg-surface-subtle' : '' }}">
                                <input type="radio" name="payment_method" value="{{ $value }}" class="form-radio"
                                    @checked(old('payment_method') == $value || (!$loop->first && old('payment_method') === null && $value === 'bank_transfer'))>
                                <span class="flex-grow text-sm font-semibold">{{ $method['label'] }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('payment_method')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="card elevation-1 bg-surface p-6 lg:p-8">
                    <label for="delivery_schedule" class="form-label">Jadwal Pengiriman <span class="font-normal text-text-muted">(opsional)</span></label>
                    <input type="datetime-local" name="delivery_schedule" id="delivery_schedule" value="{{ old('delivery_schedule') }}"
                        class="form-input @error('delivery_schedule') is-error @enderror">
                    @error('delivery_schedule')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            {{-- Summary --}}
            <div class="lg:sticky lg:top-24 h-fit">
                <div class="card elevation-1 bg-surface p-6 lg:p-8">
                    <h2 class="text-headline-sm mb-6">Ringkasan Pesanan</h2>
                    <div class="space-y-3 max-h-64 overflow-y-auto mb-6 pr-1">
                        @foreach ($cart->items as $item)
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-lg overflow-hidden bg-surface-subtle shrink-0 relative">
                                    @if ($item->product->image)
                                        <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            <svg class="w-4 h-4 text-text-muted/40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 2L2 7l10 5 10-5-10-5z"/></svg>
                                        </div>
                                    @endif
                                    <span class="absolute -top-1 -right-1 w-5 h-5 rounded-full bg-obsidian text-white text-[11px] font-bold flex items-center justify-center">{{ $item->quantity }}</span>
                                </div>
                                <div class="flex-grow min-w-0">
                                    <p class="text-sm font-medium truncate">{{ $item->product->name }}</p>
                                    <p class="text-xs text-text-muted">Rp {{ number_format((float) $item->product->price, 0, ',', '.') }}</p>
                                </div>
                                <span class="text-sm font-semibold whitespace-nowrap">Rp {{ number_format((float) $item->product->price * $item->quantity, 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>
                    <dl class="space-y-3 mb-6">
                        <div class="flex items-center justify-between text-sm">
                            <dt class="text-text-secondary">Subtotal</dt>
                            <dd class="font-semibold">Rp {{ number_format((float) $subtotal, 0, ',', '.') }}</dd>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <dt class="text-text-secondary">Ongkos kirim</dt>
                            <dd class="font-semibold text-text-muted" data-shipping-display>Belum dipilih</dd>
                        </div>
                        <div class="divider my-4!"></div>
                        <div class="flex items-center justify-between">
                            <dt class="text-title-md">Total</dt>
                            <dd class="text-price text-2xl" data-total-display>Rp {{ number_format((float) $subtotal, 0, ',', '.') }}</dd>
                        </div>
                    </dl>
                    <button type="submit" id="place-order-btn" class="btn btn-primary w-full" {{ $addresses->isEmpty() || $cart->items->isEmpty() ? 'disabled' : '' }}>
                        Buat Pesanan
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </button>
                    <a href="{{ route('cart.index') }}" class="btn btn-outline w-full mt-2">Kembali ke Keranjang</a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const courierInput = document.querySelector('[data-courier-input]');
    const shippingDisplay = document.querySelector('[data-shipping-display]');
    const totalDisplay = document.querySelector('[data-total-display]');
    const subtotal = {{ $subtotal }};

    if (!courierInput || !shippingDisplay || !totalDisplay) return;

    document.querySelectorAll('[data-shipping-option]').forEach((option) => {
        const radio = option.querySelector('input[type="radio"]');
        radio.addEventListener('change', () => {
            if (!radio.checked) return;
            courierInput.value = radio.dataset.courier;
            const cost = parseFloat(radio.dataset.cost) || 0;
            shippingDisplay.textContent = 'Rp ' + cost.toLocaleString('id-ID');
            shippingDisplay.classList.remove('text-text-muted');
            const total = subtotal + cost;
            totalDisplay.textContent = 'Rp ' + total.toLocaleString('id-ID');
        });

        if (radio.checked) {
            radio.dispatchEvent(new Event('change'));
        }
    });
});
</script>
@endpush