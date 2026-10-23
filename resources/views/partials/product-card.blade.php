@props(['product'])

@php
    $hasDiscount = $product->hasDiscount();
    $price = $product->activePrice();
    $originalPrice = (float) $product->price;
    $image = $product->primaryImage ?? ($product->image ? (object) ['image' => $product->image] : null);
    $cover = $image->image ?? null;
@endphp

<article class="product-card flex flex-col group" data-wishlist-card="{{ $product->id }}">
    <a href="{{ route('products.show', $product) }}" class="product-card-image block relative">
        @if ($cover)
            <img src="{{ asset('storage/' . $cover) }}" alt="{{ $product->name }}" loading="lazy">
        @else
            <div class="w-full h-full flex items-center justify-center bg-surface-subtle">
                <svg class="w-12 h-12 text-text-muted/40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                    <path d="M2 17l10 5 10-5"/>
                    <path d="M2 12l10 5 10-5"/>
                </svg>
            </div>
        @endif
        <span class="absolute top-3 left-3 flex flex-col gap-1">
            @if ($hasDiscount)
                <span class="badge badge-primary">Sale -{{ $product->discountPercent() }}%</span>
            @endif
        </span>
    </a>
    <div class="product-card-body flex flex-col gap-1.5 grow">
        @if ($product->category)
            <p class="product-card-category">{{ $product->category->name }}</p>
        @endif
        <a href="{{ route('products.show', $product) }}" class="product-card-name line-clamp-2">
            {{ $product->name }}
        </a>
        <div class="mt-auto pt-3 flex items-end justify-between gap-2">
            <div>
                @if ($hasDiscount)
                    <p class="text-xs text-text-muted line-through">Rp {{ number_format($originalPrice, 0, ',', '.') }}</p>
                @endif
                <p class="text-price">Rp {{ number_format($price, 0, ',', '.') }}</p>
            </div>
            @auth
                @if ($product->totalStock() > 0)
                    <form action="{{ route('cart.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="quantity" value="1">
                    <button type="submit" class="btn btn-primary btn-icon product-card-add" aria-label="Tambah {{ $product->name }} ke keranjang">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 5v14M5 12h14"/>
                            </svg>
                        </button>
                    </form>
                @else
                    <span class="badge badge-muted">Habis</span>
                @endif
            @else
                <a href="{{ route('login') }}" class="btn btn-outline btn-sm">Masuk</a>
            @endauth
        </div>
    </div>
</article>
