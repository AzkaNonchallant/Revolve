@props(['product'])

@php
    $hasDiscount = $product->hasDiscount();
    $price = $product->activePrice();
    $originalPrice = (float) $product->price;
    $image = $product->primaryImage ?? ($product->image ? (object) ['image' => $product->image] : null);
    $cover = $image->image ?? null;
@endphp

<article class="product-card group">
    <div class="product-card-image">
        <a href="{{ route('products.show', $product) }}" class="product-card-image-link" aria-label="Lihat {{ $product->name }}">
        @if ($cover)
            <img src="{{ asset('storage/' . $cover) }}" alt="{{ $product->name }}" loading="lazy">
        @else
            <div class="product-card-placeholder">
                <svg viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                    <path d="M2 17l10 5 10-5"/>
                    <path d="M2 12l10 5 10-5"/>
                </svg>
            </div>
        @endif
        </a>
        @if ($hasDiscount)
            <span class="product-sale-badge">SALE -{{ $product->discountPercent() }}%</span>
        @endif
        @auth
            <form action="{{ route('wishlist.toggle') }}" method="POST" class="product-wishlist-form">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <button type="submit" class="product-wishlist-button" aria-label="Tambah {{ $product->name }} ke wishlist">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20.8 4.6a5.4 5.4 0 0 0-7.6 0L12 5.8l-1.2-1.2a5.4 5.4 0 0 0-7.6 7.6l1.2 1.2L12 21l7.6-7.6 1.2-1.2a5.4 5.4 0 0 0 0-7.6Z"/></svg>
                </button>
            </form>
        @else
            <a href="{{ route('login') }}" class="product-wishlist-button" aria-label="Masuk untuk menyimpan {{ $product->name }} ke wishlist">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20.8 4.6a5.4 5.4 0 0 0-7.6 0L12 5.8l-1.2-1.2a5.4 5.4 0 0 0-7.6 7.6l1.2 1.2L12 21l7.6-7.6 1.2-1.2a5.4 5.4 0 0 0 0-7.6Z"/></svg>
            </a>
        @endauth
    </div>
    <div class="product-card-body">
        @if ($product->category)
            <p class="product-card-category">{{ $product->category->name }}</p>
        @endif
        <a href="{{ route('products.show', $product) }}" class="product-card-name line-clamp-2">
            {{ $product->name }}
        </a>
        <div class="product-card-price-row">
            <div>
                @if ($hasDiscount)
                    <p class="text-xs text-text-muted line-through">Rp {{ number_format($originalPrice, 0, ',', '.') }}</p>
                @endif
                <p class="text-price">Rp {{ number_format($price, 0, ',', '.') }}</p>
            </div>
            @auth
                @if ($product->totalStock() > 0)
                    <form action="{{ route('cart.store') }}" method="POST" class="product-cart-form">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" class="product-card-add" aria-label="Tambah {{ $product->name }} ke keranjang">
                            <svg viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 5v14M5 12h14"/>
                            </svg>
                        </button>
                    </form>
                @else
                    <span class="product-stock-status">Habis</span>
                @endif
            @else
                <a href="{{ route('login') }}" class="product-card-login-link" aria-label="Masuk untuk membeli {{ $product->name }}">Masuk</a>
            @endauth
        </div>
    </div>
</article>
