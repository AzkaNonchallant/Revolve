@extends('layouts.app')

@section('title', $product->name . ' — Revolve')

@section('content')
@php
    $hasDiscount = $product->hasDiscount();
    $price = $product->activePrice();
    $originalPrice = (float) $product->price;
    $totalStock = $product->totalStock();
    $cover = $product->primaryImage?->image;
    $images = $product->images->isEmpty() ? collect([(object) ['image' => $cover]]) : $product->images;
    $shop = $product->shop;
@endphp

<div class="container-app pt-8 lg:pt-12 pb-16">
    <nav class="flex items-center gap-2 text-sm text-text-muted mb-6" aria-label="Breadcrumb">
        <a href="{{ route('home') }}" class="hover:text-text-primary">Beranda</a>
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
        @if ($product->category)
            @if ($product->category->parent)
                <a href="{{ route('categories.show', $product->category->parent) }}" class="hover:text-text-primary">{{ $product->category->parent->name }}</a>
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
            @endif
            <a href="{{ route('categories.show', $product->category) }}" class="hover:text-text-primary">{{ $product->category->name }}</a>
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
        @endif
        <span class="text-text-primary font-medium truncate">{{ $product->name }}</span>
    </nav>

    @include('partials.alerts')

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 mb-12">
        {{-- Gallery --}}
        <div>
            <div class="card elevation-1 bg-surface overflow-hidden aspect-square rounded-2xl mb-4">
                <img id="gallery-main" src="{{ $images->first()->image ? asset('storage/' . $images->first()->image) : '' }}"
                    alt="{{ $product->name }}" class="w-full h-full object-cover">
            </div>
            @if ($images->count() > 1)
                <div class="grid grid-cols-5 gap-2">
                    @foreach ($images->take(5) as $image)
                        <button type="button" data-gallery-thumb data-src="{{ asset('storage/' . $image->image) }}"
                            class="aspect-square rounded-xl overflow-hidden border-2 {{ $loop->first ? 'border-obsidian' : 'border-transparent' }} hover:border-obsidian transition-colors">
                            <img src="{{ asset('storage/' . $image->image) }}" alt="" class="w-full h-full object-cover">
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Info --}}
        <div>
            @if ($shop)
                <a href="{{ route('shops.show', $shop) }}" class="inline-flex items-center gap-2 mb-4 card elevation-1 bg-surface px-3 py-2 rounded-full hover:border-obsidian transition-colors">
                    @if ($shop->logo)
                        <img src="{{ asset('storage/' . $shop->logo) }}" alt="{{ $shop->name }}" class="w-6 h-6 rounded-full object-cover">
                    @else
                        <span class="w-6 h-6 rounded-full bg-obsidian text-white text-xs font-bold flex items-center justify-center">{{ strtoupper(substr($shop->name, 0, 1)) }}</span>
                    @endif
                    <span class="text-sm font-semibold">{{ $shop->name }}</span>
                    @if ($shop->is_verified)
                        <svg class="w-4 h-4 text-primary" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.2l-3.5-3.5L4 14.2 9 19l11-11-1.5-1.5z"/></svg>
                    @endif
                </a>
            @endif

            <h1 class="text-headline-lg mb-3">{{ $product->name }}</h1>

            <div class="flex items-center gap-3 mb-4">
                <span class="stars sm">
                    @for ($i = 1; $i <= 5; $i++)
                        <svg viewBox="0 0 24 24" fill="{{ $i <= round($avgRating) ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2" class="{{ $i > round($avgRating) ? 'muted' : '' }}"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                    @endfor
                </span>
                <span class="text-sm text-text-muted">
                    {{ $avgRating > 0 ? number_format($avgRating, 1, ',', '.') . ' / 5 · ' : '' }}{{ $product->reviews->count() }} ulasan
                </span>
            </div>

            <div class="card elevation-1 bg-surface p-5 lg:p-6 mb-6">
                <div class="flex flex-wrap items-end gap-x-4 gap-y-1 mb-2">
                    <p class="text-price text-3xl">Rp {{ number_format($price, 0, ',', '.') }}</p>
                    @if ($hasDiscount)
                        <p class="text-text-muted text-line-through">Rp {{ number_format($originalPrice, 0, ',', '.') }}</p>
                        <span class="badge badge-primary">Hemat {{ $product->discountPercent() }}%</span>
                    @endif
                </div>
                <p class="text-sm text-text-muted">
                    Stok: <span class="font-semibold text-text-primary">{{ $totalStock }}</span> {{ $totalStock <= 5 ? '(hampir habis!)' : '' }}
                </p>
            </div>

            <form action="{{ route('cart.store') }}" method="POST" id="add-to-cart-form">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" id="variant-input" name="variant_id" value="">
                <input type="hidden" id="qty-input" name="quantity" value="1">

                @if ($product->variants->isNotEmpty())
                    <div class="mb-4">
                        <p class="text-label-sm text-text-secondary mb-2">Warna & Ukuran</p>
                        <div class="variant-selector">
                            @foreach ($product->variants as $variant)
                                <button type="button" data-variant data-id="{{ $variant->id }}"
                                    data-stock="{{ $variant->stock }}"
                                    data-price="{{ (float) $variant->price }}" data-price-formatted="Rp {{ number_format((float) ($variant->price > 0 ? $variant->price : $price), 0, ',', '.') }}"
                                    class="variant-option {{ $variant->stock <= 0 ? 'is-disabled' : '' }}"
                                    {{ $variant->stock <= 0 ? 'disabled' : '' }}>
                                    {{ $variant->label }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="flex flex-wrap items-center gap-4 mb-6">
                    <div class="flex items-center gap-1">
                        <button type="button" data-qty-minus class="w-10 h-10 flex items-center justify-center rounded-lg border border-border text-text-secondary hover:bg-surface-subtle transition-colors" aria-label="Kurangi jumlah">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M5 12h14"/></svg>
                        </button>
                        <span class="w-12 text-center text-sm font-bold" data-qty-display>1</span>
                        <button type="button" data-qty-plus class="w-10 h-10 flex items-center justify-center rounded-lg border border-border text-text-secondary hover:bg-surface-subtle transition-colors" aria-label="Tambah jumlah">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
                        </button>
                    </div>
                    <div class="flex gap-2 flex-grow">
                        @auth
                            <button type="submit" class="btn btn-primary flex-grow" {{ $totalStock <= 0 ? 'disabled' : '' }}>
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                                Tambah ke Keranjang
                            </button>
                            <button type="button" data-wishlist-toggle data-product="{{ $product->id }}"
                                class="w-12 h-12 flex items-center justify-center rounded-lg border {{ $isWishlisted ? 'bg-error text-white border-error' : 'border-border text-text-secondary hover:text-error hover:border-error' }} transition-colors"
                                aria-label="Tambah ke wishlist">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7z"/>
                                </svg>
                            </button>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-primary flex-grow">Tambah ke Keranjang</a>
                        @endauth
                    </div>
                </div>
            </form>

            <div class="space-y-2 text-sm text-text-secondary">
                @if ($product->brand)
                    <p class="flex items-center gap-2"><span class="badge badge-muted">Brand</span> {{ $product->brand->name }}</p>
                @endif
                @if ($product->material)
                    <p class="flex items-center gap-2"><span class="badge badge-muted">Bahan</span> {{ $product->material }}</p>
                @endif
                <p class="flex items-center gap-2"><span class="badge badge-muted">Berat</span> {{ number_format($product->weight, 0, ',', '.') }} g</p>
                <p class="flex items-center gap-2"><span class="badge badge-muted">Terjual</span> {{ $soldCount }} item</p>
            </div>
        </div>
    </div>

    {{-- Description --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-12">
        <div class="lg:col-span-2">
            <div class="card elevation-1 bg-surface p-6 lg:p-8">
                <h2 class="text-headline-sm mb-4">Deskripsi Produk</h2>
                <div class="prose max-w-none">
                    @if ($product->description)
                        {!! nl2br(e($product->description)) !!}
                    @else
                        <p class="text-text-muted">Tidak ada deskripsi.</p>
                    @endif
                </div>
            </div>

            {{-- Reviews --}}
            <div class="card elevation-1 bg-surface p-6 lg:p-8 mt-8">
                <h2 class="text-headline-sm mb-6">Ulasan Pembeli</h2>
                <div class="grid grid-cols-1 md:grid-cols-[240px_1fr] gap-8 mb-8">
                    <div class="text-center">
                        <p class="text-display-sm font-heading font-extrabold">{{ number_format($avgRating, 1, ',', '.') }}</p>
                        <span class="stars justify-center my-2">
                            @for ($i = 1; $i <= 5; $i++)
                                <svg viewBox="0 0 24 24" fill="{{ $i <= round($avgRating) ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2" class="{{ $i > round($avgRating) ? 'muted' : '' }}"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                            @endfor
                        </span>
                        <p class="text-sm text-text-muted">{{ $product->reviews->count() }} ulasan</p>
                    </div>
                    <div class="space-y-2">
                        @foreach (array_reverse($ratingCounts, true) as $rating => $count)
                            <div class="flex items-center gap-3 text-sm">
                                <span class="w-8 shrink-0 text-text-muted">{{ $rating }} ★</span>
                                <div class="flex-grow h-2 rounded-full bg-surface-muted overflow-hidden">
                                    <div class="h-full bg-primary rounded-full" style="width: {{ $product->reviews->count() ? $count / max($product->reviews->count(), 1) * 100 : 0 }}%"></div>
                                </div>
                                <span class="w-6 text-right text-text-muted">{{ $count }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                @if ($product->reviews->isNotEmpty())
                    <div class="divide-y divide-surface-muted">
                        @foreach ($product->reviews as $review)
                            <div class="py-5 first:pt-0">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-3">
                                        <span class="w-9 h-9 rounded-full bg-obsidian text-white text-sm font-bold flex items-center justify-center">{{ strtoupper(substr($review->user?->name ?? 'U', 0, 1)) }}</span>
                                        <div>
                                            <p class="text-sm font-semibold">{{ $review->user?->name ?? 'Pengguna' }}</p>
                                            <span class="stars sm">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <svg viewBox="0 0 24 24" fill="{{ $i <= $review->rating ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2" class="{{ $i > $review->rating ? 'muted' : '' }}"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                                @endfor
                                            </span>
                                        </div>
                                    </div>
                                    <span class="text-xs text-text-muted">{{ $review->created_at->diffForHumans() }}</span>
                                </div>
                                @if ($review->comment)
                                    <p class="text-sm text-text-secondary leading-relaxed">{{ $review->comment }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="card bg-surface-subtle p-6 text-center">
                        <p class="text-text-muted">Belum ada ulasan untuk produk ini.</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Sidebar info --}}
        <div class="space-y-4 lg:sticky lg:top-24 h-fit">
            @if ($shop)
                <div class="card elevation-1 bg-surface p-6">
                    <h3 class="text-title-md font-semibold mb-2">{{ $shop->name }}</h3>
                    <p class="text-sm text-text-muted mb-3">{{ $shop->description ?: 'Seller Revolve.' }}</p>
                    <a href="{{ route('shops.show', $shop) }}" class="btn btn-outline w-full">Kunjungi Toko</a>
                </div>
            @endif
            <div class="card elevation-1 bg-surface p-6">
                <h3 class="text-title-md font-semibold mb-3">Pengiriman</h3>
                <ul class="space-y-2 text-sm text-text-secondary">
                    <li class="flex items-center gap-2">· Estimasi 1–4 hari kerja</li>
                    <li class="flex items-center gap-2">· Berat {{ number_format($product->weight, 0, ',', '.') }} g</li>
                    <li class="flex items-center gap-2">· Pengiriman ke seluruh Indonesia</li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Related --}}
    @if ($relatedProducts->isNotEmpty())
        <section>
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-headline-lg">Produk Serupa</h2>
                <a href="{{ route('products.index') }}" class="text-sm font-semibold text-primary hover:text-primary-hover">Lihat Semua →</a>
            </div>
            <div class="grid-products">
                @foreach ($relatedProducts as $related)
                    @include('partials.product-card', ['product' => $related])
                @endforeach
            </div>
        </section>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const main = document.getElementById('gallery-main');
    document.querySelectorAll('[data-gallery-thumb]').forEach((thumb) => {
        thumb.addEventListener('click', () => {
            main.src = thumb.dataset.src;
            document.querySelectorAll('[data-gallery-thumb]').forEach((t) => t.classList.remove('border-obsidian'));
            thumb.classList.add('border-obsidian');
        });
    });

    const qtyInput = document.getElementById('qty-input');
    const qtyDisplay = document.querySelector('[data-qty-display]');
    const minus = document.querySelector('[data-qty-minus]');
    const plus = document.querySelector('[data-qty-plus]');

    const maxStock = () => {
        const selected = document.querySelector('[data-variant].is-selected');
        return selected ? parseInt(selected.dataset.stock, 10) : {{ $totalStock }};
    };

    const refresh = () => {
        const value = parseInt(qtyInput.value, 10) || 1;
        qtyDisplay.textContent = value;
    };

    minus?.addEventListener('click', () => {
        const current = parseInt(qtyInput.value, 10) || 1;
        if (current > 1) { qtyInput.value = current - 1; refresh(); }
    });
    plus?.addEventListener('click', () => {
        const current = parseInt(qtyInput.value, 10) || 1;
        const max = maxStock();
        if (current >= max) { alert('Stok tidak mencukupi'); return; }
        qtyInput.value = current + 1; refresh();
    });

    document.querySelectorAll('[data-variant]').forEach((variant) => {
        variant.addEventListener('click', () => {
            document.querySelectorAll('[data-variant]').forEach((v) => v.classList.remove('is-selected'));
            variant.classList.add('is-selected');
            document.getElementById('variant-input').value = variant.dataset.id;
            if (parseInt(qtyInput.value, 10) > parseInt(variant.dataset.stock, 10)) {
                qtyInput.value = variant.dataset.stock > 0 ? variant.dataset.stock : 1;
                refresh();
            }
        });
    });

    const wishlist = document.querySelector('[data-wishlist-toggle]');
    if (wishlist) {
        wishlist.addEventListener('click', async () => {
            const res = await fetch('{{ route('wishlist.toggle') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ product_id: wishlist.dataset.product })
            });
            const data = await res.json();
            if (res.ok && data.status === 'added') {
                wishlist.classList.remove('border-border', 'text-text-secondary');
                wishlist.classList.add('bg-error', 'text-white', 'border-error');
                wishlist.dataset.wishlisted = '1';
            } else {
                wishlist.classList.add('border-border', 'text-text-secondary');
                wishlist.classList.remove('bg-error', 'text-white', 'border-error');
                wishlist.dataset.wishlisted = '0';
            }
        });
    }
});
</script>
@endpush