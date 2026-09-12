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
        <span class="absolute top-2 left-2 flex flex-col gap-1">
            @if ($hasDiscount)
                <span class="badge badge-primary">-{{ $product->discountPercent() }}%</span>
            @endif
        </span>
        @if ($product->category)
            <span class="badge badge-muted absolute top-2 right-2">{{ $product->category->name }}</span>
        @endif
    </a>
    <div class="product-card-body flex flex-col gap-1.5 grow">
        @if ($product->shop)
            <p class="text-label-sm text-text-muted flex items-center gap-1">
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l1-5h16l1 5"/><path d="M3 9a3 3 0 0 0 6 0 3 3 0 0 0 6 0 3 3 0 0 0 6 0"/><path d="M5 12v7h14v-7"/></svg>
                {{ $product->shop->name }}
            </p>
        @else
            <span class="text-label-sm text-text-muted">Revolve</span>
        @endif
        <a href="{{ route('products.show', $product) }}" class="text-title-md text-text-primary line-clamp-2 hover:text-primary transition-colors">
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
                        <button type="submit" class="btn btn-primary btn-sm" aria-label="Tambah ke keranjang">
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