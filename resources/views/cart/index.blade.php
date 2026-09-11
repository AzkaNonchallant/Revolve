@extends('layouts.app')

@section('title', 'Keranjang — Revolve')

@section('content')
<div class="container-app pt-8 lg:pt-12 pb-16">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <h1 class="text-headline-lg">Keranjang</h1>
        @if ($cart->items->isNotEmpty())
            <form action="{{ route('cart.clear') }}" method="POST" onsubmit="return confirm('Kosongkan seluruh keranjang?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-sm font-semibold text-error hover:text-error/70 transition-colors">Kosongkan Keranjang</button>
            </form>
        @endif
    </div>

    @include('partials.alerts')

    @if ($cart->items->isNotEmpty())
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Items --}}
            <div class="lg:col-span-2 space-y-4">
                @php
                    $subtotal = $cart->items->sum(fn ($item) => (float) $item->product->price * $item->quantity);
                @endphp
                @foreach ($cart->items as $item)
                    <div class="card elevation-1 bg-surface p-4 lg:p-5 flex flex-col sm:flex-row gap-4">
                        <a href="{{ route('products.show', $item->product) }}" class="w-24 h-24 rounded-lg overflow-hidden bg-surface-subtle shrink-0">
                            @if ($item->product->image)
                                <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <svg class="w-8 h-8 text-text-muted/40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                                        <path d="M2 17l10 5 10-5"/>
                                        <path d="M2 12l10 5 10-5"/>
                                    </svg>
                                </div>
                            @endif
                        </a>
                        <div class="flex-grow flex flex-col gap-2 sm:flex-row sm:items-center justify-between">
                            <div>
                                @if ($item->product->category)
                                    <p class="text-label-sm text-text-muted mb-0.5">{{ $item->product->category->name }}</p>
                                @endif
                                <a href="{{ route('products.show', $item->product) }}" class="text-title-md hover:text-primary transition-colors line-clamp-1">{{ $item->product->name }}</a>
                                <p class="text-sm text-text-secondary mt-0.5">Rp {{ number_format((float) $item->product->price, 0, ',', '.') }}</p>
                                @if ($item->product->stock <= 0)
                                    <p class="text-xs text-error font-semibold mt-1">Stok sudah habis</p>
                                @endif
                            </div>
                            <div class="flex items-center gap-3">
                                <form action="{{ route('cart.update', $item) }}" method="POST" class="flex items-center gap-1">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="quantity" data-qty-input value="{{ $item->quantity }}" min="1" max="{{ $item->product->stock }}">
                                    <button type="button" data-qty-minus class="w-9 h-9 flex items-center justify-center rounded-lg border border-border text-text-secondary hover:bg-surface-subtle transition-colors" aria-label="Kurangi jumlah">
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M5 12h14"/></svg>
                                    </button>
                                    <span class="w-10 text-center text-sm font-semibold" data-qty-display>{{ $item->quantity }}</span>
                                    <button type="button" data-qty-plus class="w-9 h-9 flex items-center justify-center rounded-lg border border-border text-text-secondary hover:bg-surface-subtle transition-colors" aria-label="Tambah jumlah">
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
                                    </button>
                                    <button type="submit" class="btn btn-outline btn-sm h-9! hidden" data-qty-save>Simpan</button>
                                </form>
                                <form action="{{ route('cart.destroy', $item) }}" method="POST" onsubmit="return confirm('Hapus produk dari keranjang?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-9 h-9 flex items-center justify-center rounded-lg border border-border text-text-secondary hover:text-error hover:border-error transition-colors" aria-label="Hapus">
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2m3 0v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Summary --}}
            <div class="lg:sticky lg:top-24 h-fit">
                <div class="card elevation-1 bg-surface p-6 lg:p-8">
                    <h2 class="text-headline-sm mb-6">Ringkasan Belanja</h2>
                    <dl class="space-y-3 mb-6">
                        <div class="flex items-center justify-between text-sm">
                            <dt class="text-text-secondary">Subtotal ({{ $cart->items->count() }} produk)</dt>
                            <dd class="font-semibold">Rp {{ number_format((float) $subtotal, 0, ',', '.') }}</dd>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <dt class="text-text-secondary">Ongkos kirim</dt>
                            <dd class="text-text-muted">Dihitung saat checkout</dd>
                        </div>
                        <div class="divider my-4!"></div>
                        <div class="flex items-center justify-between">
                            <dt class="text-title-md">Total</dt>
                            <dd class="text-price text-2xl">Rp {{ number_format((float) $subtotal, 0, ',', '.') }}</dd>
                        </div>
                    </dl>
                    <a href="{{ route('checkout.show') }}" class="btn btn-primary w-full mb-3">
                        Lanjut ke Checkout
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </a>
                    <a href="{{ route('products.index') }}" class="btn btn-outline w-full">Lanjut Belanja</a>
                </div>
            </div>
        </div>
    @else
        <div class="empty-state card elevation-1 bg-surface">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="9" cy="21" r="1"/>
                <circle cx="20" cy="21" r="1"/>
                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
            </svg>
            <h3 class="text-title-md mb-2">Keranjang masih kosong</h3>
            <p>Yuk mulai belanja produk kesehatan favorit Anda.</p>
            <a href="{{ route('products.index') }}" class="btn btn-primary">Jelajahi Produk</a>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('form[data-qty-input]').forEach((form) => {
        const input = form.querySelector('[data-qty-input]');
        const display = form.querySelector('[data-qty-display]');
        const minus = form.querySelector('[data-qty-minus]');
        const plus = form.querySelector('[data-qty-plus]');
        const save = form.querySelector('[data-qty-save]');

        if (!input || !display || !minus || !plus || !save) return;

        const max = parseInt(input.max, 10) || 99;
        const sync = () => {
            const value = parseInt(input.value, 10) || 1;
            display.textContent = value;
            save.classList.toggle('hidden', value === parseInt(input.dataset.original, 10));
        };

        input.dataset.original = input.value;
        save.classList.add('hidden');

        minus.addEventListener('click', () => {
            const current = parseInt(input.value, 10) || 1;
            if (current > 1) {
                input.value = current - 1;
                sync();
            }
        });
        plus.addEventListener('click', () => {
            const current = parseInt(input.value, 10) || 1;
            if (current < max) {
                input.value = current + 1;
                sync();
            }
        });
    });
});
</script>
@endpush