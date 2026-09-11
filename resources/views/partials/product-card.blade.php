@props(['product'])

<article class="product-card flex flex-col">
    <a href="{{ route('products.show', $product) }}" class="product-card-image block">
        @if ($product->image)
            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" loading="lazy">
        @else
            <div class="w-full h-full flex items-center justify-center bg-surface-subtle">
                <svg class="w-12 h-12 text-text-muted/40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                    <path d="M2 17l10 5 10-5"/>
                    <path d="M2 12l10 5 10-5"/>
                </svg>
            </div>
        @endif
        @if ($product->category)
            <span class="badge badge-muted absolute top-2 left-2">{{ $product->category->name }}</span>
        @endif
    </a>
    <div class="product-card-body flex flex-col gap-1.5 grow">
        <div class="flex items-center justify-between gap-2">
            <span class="text-label-sm text-text-muted">{{ $product->category?->name ?? 'Produk' }}</span>
            @if ($product->healthy_score && $product->healthy_score >= 80)
                <span class="badge badge-primary" title="Skor kesehatan">Sehat</span>
            @elseif ($product->healthy_score && $product->healthy_score >= 60)
                <span class="badge badge-muted" title="Skor kesehatan {{ $product->healthy_score }}">{{ $product->healthy_score }}/100</span>
            @endif
        </div>
        <a href="{{ route('products.show', $product) }}" class="text-title-md text-text-primary line-clamp-2 hover:text-primary transition-colors">
            {{ $product->name }}
        </a>
        <div class="mt-auto pt-3 flex items-end justify-between gap-2">
            <div>
                <p class="text-price">Rp {{ number_format((float) $product->price, 0, ',', '.') }}</p>
                <p class="text-xs text-text-muted">{{ $product->weight ? number_format($product->weight, 0, ',', '.') . ' g' : '' }}</p>
            </div>
            @auth
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
                <a href="{{ route('login') }}" class="btn btn-outline btn-sm">Masuk</a>
            @endauth
        </div>
    </div>
</article>