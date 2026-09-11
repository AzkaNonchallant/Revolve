@extends('layouts.app')

@section('title', $product->name . ' — Revolve')

@section('content')
<div class="container-app pt-8 lg:pt-12 pb-16">
    @include('partials.alerts')

    <nav class="flex items-center gap-2 text-sm text-text-muted mb-8" aria-label="Breadcrumb">
        <a href="{{ route('home') }}" class="hover:text-text-primary">Beranda</a>
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
        <a href="{{ route('products.index') }}" class="hover:text-text-primary">Produk</a>
        @if ($product->category)
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
            <a href="{{ route('categories.show', $product->category) }}" class="hover:text-text-primary">{{ $product->category->name }}</a>
        @endif
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
        <span class="text-text-primary font-medium truncate">{{ $product->name }}</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 mb-16">
        {{-- Image --}}
        <div class="card elevation-1 overflow-hidden aspect-square bg-surface-subtle">
            @if ($product->image)
                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
            @else
                <div class="w-full h-full flex items-center justify-center">
                    <svg class="w-24 h-24 text-text-muted/40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                        <path d="M2 17l10 5 10-5"/>
                        <path d="M2 12l10 5 10-5"/>
                    </svg>
                </div>
            @endif
        </div>

        {{-- Info --}}
        <div class="flex flex-col">
            <div class="flex items-center gap-2 mb-2">
                @if ($product->category)
                    <span class="badge badge-muted">{{ $product->category->name }}</span>
                @endif
                <span class="badge badge-primary">Skor Kesehatan {{ $product->healthy_score }}/100</span>
            </div>
            <h1 class="text-headline-lg mb-3">{{ $product->name }}</h1>
            <p class="text-price text-3xl mb-6">Rp {{ number_format((float) $product->price, 0, ',', '.') }}</p>

            <div class="flex items-center gap-6 py-4 border-y border-surface-muted mb-6">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-text-muted" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 7h-9M14 17H5"/>
                        <circle cx="17" cy="17" r="3"/>
                        <circle cx="7" cy="7" r="3"/>
                    </svg>
                    <span class="text-sm text-text-secondary">{{ number_format($product->weight, 0, ',', '.') }} g</span>
                </div>
                <div class="flex items-center gap-2">
                    @if ($product->stock > 0)
                        <span class="w-2 h-2 rounded-full bg-success"></span>
                        <span class="text-sm text-text-secondary">
                            {{ $product->stock <= 5 ? 'Sisa ' . $product->stock . ' stok!' : 'Stok tersedia' }}
                        </span>
                    @else
                        <span class="w-2 h-2 rounded-full bg-error"></span>
                        <span class="text-sm text-error font-semibold">Stok habis</span>
                    @endif
                </div>
            </div>

            <p class="text-text-secondary leading-relaxed mb-8">{!! nl2br(e($product->description)) !!}</p>

            @auth
                <form action="{{ route('cart.store') }}" method="POST" class="mt-auto">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center border border-border rounded-lg overflow-hidden h-12">
                            <button type="button" data-qty-minus class="w-11 h-full flex items-center justify-center text-text-secondary hover:bg-surface-subtle transition-colors" aria-label="Kurangi jumlah">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M5 12h14"/></svg>
                            </button>
                            <input type="number" name="quantity" data-qty-input value="1" min="1" max="{{ $product->stock }}" inputmode="numeric"
                                class="w-14 h-full text-center text-sm font-semibold outline-none border-x border-border bg-surface" aria-label="Jumlah">
                            <button type="button" data-qty-plus class="w-11 h-full flex items-center justify-center text-text-secondary hover:bg-surface-subtle transition-colors" aria-label="Tambah jumlah">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
                            </button>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg flex-grow" @disabled($product->stock <= 0)>
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="9" cy="21" r="1"/>
                                <circle cx="20" cy="21" r="1"/>
                                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                            </svg>
                            {{ $product->stock > 0 ? 'Tambah ke Keranjang' : 'Stok Habis' }}
                        </button>
                    </div>
                </form>
            @else
                <div class="mt-auto card bg-surface-subtle p-4 rounded-lg">
                    <p class="text-sm text-text-secondary mb-3">Masuk untuk menambahkan produk ini ke keranjang.</p>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('login') }}" class="btn btn-primary btn-sm">Masuk</a>
                        <a href="{{ route('register') }}" class="btn btn-outline btn-sm">Daftar</a>
                    </div>
                </div>
            @endauth
        </div>
    </div>

    {{-- Nutrition --}}
    @if ($product->nutrition)
        <div class="mb-16">
            <h2 class="text-headline-md mb-6">Informasi Nutrisi</h2>
            <div class="card elevation-1 p-6 lg:p-8 bg-surface">
                <p class="text-text-secondary whitespace-pre-line leading-relaxed">{!! nl2br(e($product->nutrition)) !!}</p>
            </div>
        </div>
    @endif

    {{-- Related Products --}}
    @if ($relatedProducts->isNotEmpty())
        <div>
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-headline-md">Produk Terkait</h2>
                @if ($product->category)
                    <a href="{{ route('categories.show', $product->category) }}" class="text-sm font-semibold text-primary hover:text-primary-hover">Lihat Semua →</a>
                @endif
            </div>
            <div class="grid-products">
                @foreach ($relatedProducts as $related)
                    @include('partials.product-card', ['product' => $related])
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const minus = document.querySelector('[data-qty-minus]');
    const plus = document.querySelector('[data-qty-plus]');
    const input = document.querySelector('[data-qty-input]');

    if (minus && plus && input) {
        const max = parseInt(input.max, 10) || 99;
        minus.addEventListener('click', () => {
            const current = parseInt(input.value, 10) || 1;
            if (current > 1) input.value = current - 1;
        });
        plus.addEventListener('click', () => {
            const current = parseInt(input.value, 10) || 1;
            if (current < max) input.value = current + 1;
        });
        input.addEventListener('change', () => {
            let value = parseInt(input.value, 10) || 1;
            value = Math.min(Math.max(value, 1), max);
            input.value = value;
        });
    }
});
</script>
@endpush