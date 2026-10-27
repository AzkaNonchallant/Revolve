@extends('layouts.app')

@section('title', 'Wishlist — Revolve')

@section('content')
<div class="container-app pt-8 lg:pt-12 pb-16">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <h1 class="text-headline-lg">Wishlist</h1>
        <a href="{{ route('products.index') }}" class="text-sm font-semibold text-primary hover:text-primary-hover">Jelajahi Produk →</a>
    </div>

    @include('partials.alerts')

    @if ($wishlist->items->isNotEmpty())
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
            @foreach ($wishlist->items as $item)
                @php
                    $product = $item->product;
                    $cover = $product?->primaryImage?->image ?? $product?->image;
                    $price = $item->variant && $item->variant->price != null
                        ? (float) $item->variant->price
                        : $product->activePrice();
                @endphp
                <article class="card elevation-1 bg-surface overflow-hidden flex flex-col">
                    <a href="{{ route('products.show', $product) }}" class="relative block aspect-square overflow-hidden bg-surface-subtle">
                        @if ($cover)
                            <img src="{{ asset('storage/' . $cover) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <svg class="w-12 h-12 text-text-muted/40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
                            </div>
                        @endif
                        @if ($product->hasDiscount())
                            <span class="badge badge-primary absolute top-2 left-2">-{{ $product->discountPercent() }}%</span>
                        @endif
                    </a>
                    <div class="p-4 flex flex-col grow gap-1">
                        @if ($product->shop)
                            <p class="text-label-sm text-text-muted">{{ $product->shop->name }}</p>
                        @endif
                        <a href="{{ route('products.show', $product) }}" class="text-title-md line-clamp-2 hover:text-primary transition-colors">{{ $product->name }}</a>
                        @if ($item->variant)
                            <p class="text-xs text-text-muted">Varian: {{ $item->variant->label }}</p>
                        @endif
                        <p class="text-price mt-auto pt-3">Rp {{ number_format($price, 0, ',', '.') }}</p>
                        <div class="flex gap-2 mt-2">
                            @if ($product && $product->published()->exists() && ($item->variant ? $item->variant->stock > 0 : $product->totalStock() > 0))
                                <form action="{{ route('wishlist.move-to-cart', $item) }}" method="POST" class="flex-grow">
                                    @csrf
                                    <button type="submit" class="btn btn-primary btn-sm w-full">Pindah ke Keranjang</button>
                                </form>
                            @else
                                <span class="badge badge-muted flex-grow text-center">Stok habis</span>
                            @endif
                            <form action="{{ route('wishlist.destroy', $item) }}" method="POST" onsubmit="return confirm('Hapus dari wishlist?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline btn-sm" aria-label="Hapus dari wishlist">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2m3 0v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    @else
        <div class="empty-state card elevation-1 bg-surface">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7z"/>
            </svg>
            <h3 class="text-title-md mb-2">Wishlist masih kosong</h3>
            <p>Simpan produk favorit Anda dengan menekan ikon hati pada halaman produk.</p>
            <a href="{{ route('products.index') }}" class="btn btn-primary">Jelajahi Produk</a>
        </div>
    @endif
</div>
@endsection