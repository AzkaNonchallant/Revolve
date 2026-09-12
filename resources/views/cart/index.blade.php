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
                    $selectedItems = $cart->items->filter(fn ($i) => $i->is_selected);
                @endphp
                @foreach ($cart->items as $item)
                    @php
                        $price = $item->effectivePrice();
                        $original = (float) $item->product->price;
                        $hasDiscount = $item->product->hasDiscount();
                        $cover = $item->product->primaryImage?->image ?? $item->product->image;
                        $isSelected = (bool) $item->is_selected;
                    @endphp
                    <div class="card elevation-1 bg-surface p-4 lg:p-5 flex flex-col sm:flex-row gap-4">
                        <button type="button" data-select-toggle data-action="{{ route('cart.select', $item) }}"
                            class="w-6 h-6 shrink-0 mt-1 rounded-md border-2 flex items-center justify-center transition-colors {{ $isSelected ? 'bg-primary border-primary' : 'border-border' }}" aria-label="Pilih produk">
                            @if ($isSelected)
                                <svg class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                            @endif
                        </button>
                        <a href="{{ route('products.show', $item->product) }}" class="w-24 h-24 rounded-lg overflow-hidden bg-surface-subtle shrink-0">
                            @if ($cover)
                                <img src="{{ asset('storage/' . $cover) }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
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
                                @if ($item->variant)
                                    <p class="text-sm text-text-secondary mt-0.5">Varian: {{ $item->variant->label }}</p>
                                @endif
                                <p class="text-sm text-text-secondary mt-0.5">
                                    @if ($hasDiscount)
                                        <span class="text-text-muted line-through mr-1">Rp {{ number_format($original, 0, ',', '.') }}</span>
                                    @endif
                                    Rp {{ number_format($price, 0, ',', '.') }}
                                </p>
                                @if (($item->variant ? $item->variant->stock : $item->product->stock) <= 0)
                                    <p class="text-xs text-error font-semibold mt-1">Stok sudah habis</p>
                                @endif
                            </div>
                            <div class="flex items-center gap-3">
                                <form action="{{ route('cart.update', $item) }}" method="POST" class="flex items-center gap-1" data-qty-form>
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="quantity" data-qty-input value="{{ $item->quantity }}"
                                        min="1" max="{{ $item->variant ? $item->variant->stock : $item->product->stock }}"
                                        data-original="{{ $item->quantity }}">
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
                <form action="{{ route('cart.select', '__all__') }}" method="POST" id="select-all-form" class="hidden">
                    @csrf
                    @method('PATCH')
                </form>
            </div>

            {{-- Summary --}}
            <div class="lg:sticky lg:top-24 h-fit">
                <div class="card elevation-1 bg-surface p-6 lg:p-8">
                    <h2 class="text-headline-sm mb-6">Ringkasan Belanja</h2>
                    <dl class="space-y-3 mb-6">
                        <div class="flex items-center justify-between text-sm">
                            <dt class="text-text-secondary">Produk dipilih</dt>
                            <dd class="font-semibold">{{ $selectedItems->count() }} item</dd>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <dt class="text-text-secondary">Subtotal</dt>
                            <dd class="font-semibold">Rp {{ number_format($subtotal, 0, ',', '.') }}</dd>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <dt class="text-text-secondary">Ongkos kirim</dt>
                            <dd class="text-text-muted">Dihitung saat checkout</dd>
                        </div>
                        <div class="divider my-4!"></div>
                        <div class="flex items-center justify-between">
                            <dt class="text-title-md">Total</dt>
                            <dd class="text-price text-2xl">Rp {{ number_format($subtotal, 0, ',', '.') }}</dd>
                        </div>
                    </dl>
                    <a href="{{ route('checkout.show') }}" class="btn btn-primary w-full mb-3 {{ $selectedItems->isEmpty() ? 'opacity-50 pointer-events-none' : '' }}">
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
            <p>Yuk mulai belanja fashion favorit Anda.</p>
            <a href="{{ route('products.index') }}" class="btn btn-primary">Jelajahi Produk</a>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('form[data-qty-form]').forEach((form) => {
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

        minus.addEventListener('click', () => {
            const current = parseInt(input.value, 10) || 1;
            if (current > 1) { input.value = current - 1; sync(); }
        });
        plus.addEventListener('click', () => {
            const current = parseInt(input.value, 10) || 1;
            if (current < max) { input.value = current + 1; sync(); }
        });
    });

    document.querySelectorAll('[data-select-toggle]').forEach((btn) => {
        btn.addEventListener('click', () => {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = btn.dataset.action;
            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = document.querySelector('meta[name="csrf-token"]').content;
            const method = document.createElement('input');
            method.type = 'hidden';
            method.name = '_method';
            method.value = 'PATCH';
            form.appendChild(csrf);
            form.appendChild(method);
            document.body.appendChild(form);
            form.submit();
        });
    });
});
</script>
@endpush