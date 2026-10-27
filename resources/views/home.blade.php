@extends('layouts.app')

@section('title', 'Revolve | Pilihan Style Harian')

@section('content')
    @if ($heroBanners->isNotEmpty())
        <section class="home-hero bg-surface">
            <div data-hero-slider class="relative" role="region" aria-label="Kampanye Revolve" aria-roledescription="carousel">
                @foreach ($heroBanners as $banner)
                    <div class="data-hero-slide {{ $loop->first ? '' : 'hidden' }}" data-hero-slide role="group" aria-roledescription="slide" aria-label="Slide {{ $loop->iteration }} dari {{ $heroBanners->count() }}" aria-hidden="{{ $loop->first ? 'false' : 'true' }}">
                        <div class="home-hero-layout">
                            <div class="home-hero-copy">
                                <p class="home-hero-eyebrow">Koleksi Terbaru</p>
                                <h1 class="home-hero-title"><span>Gaya yang bergerak</span><span>bersama kamu.</span></h1>
                                <p class="home-hero-description">Temukan pilihan fashion terbaru untuk melengkapi setiap aktivitas dan gaya harianmu.</p>
                                <div class="home-hero-actions">
                                    <a href="{{ $banner->link ?: route('products.index') }}" class="btn btn-primary btn-lg">Belanja koleksi</a>
                                    <a href="{{ route('products.index') }}" class="btn btn-outline btn-lg">Lihat semua produk</a>
                                </div>
                            </div>
                            <div class="home-hero-media">
                                @if ($banner->image)
                                    <img src="{{ asset('storage/' . $banner->image) }}" alt="Kampanye fashion Revolve" class="home-hero-product-image">
                                @else
                                    <img src="{{ asset('images/revolve-hero-shoe.png') }}" alt="Sepatu hitam putih" class="home-hero-product-image">
                                @endif
                            </div>
                            <div class="home-hero-caption">Pilihan Revolve</div>
                        </div>
                    </div>
                @endforeach
                @if ($heroBanners->count() > 1)
                    <div class="home-hero-controls" aria-label="Kontrol kampanye">
                        <button type="button" data-hero-prev class="hero-slider-control" aria-label="Kampanye sebelumnya">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
                        </button>
                        @foreach ($heroBanners as $banner)
                            <button type="button" data-hero-dot class="w-11 h-11 flex items-center justify-center" aria-label="Tampilkan slide {{ $loop->iteration }}" aria-pressed="{{ $loop->first ? 'true' : 'false' }}"><span class="home-hero-dot {{ $loop->first ? 'is-active' : '' }}"></span></button>
                        @endforeach
                        <button type="button" data-hero-next class="hero-slider-control" aria-label="Kampanye berikutnya">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
                        </button>
                    </div>
                @endif
            </div>
        </section>
    @else
        <section class="home-hero home-hero-fallback">
            <div class="home-hero-layout">
                <div class="home-hero-copy">
                    <p class="home-hero-eyebrow">Koleksi Terbaru</p>
                    <h1 class="home-hero-title"><span>Gaya yang bergerak</span><span>bersama kamu.</span></h1>
                    <p class="home-hero-description">Temukan pilihan fashion terbaru untuk melengkapi setiap aktivitas dan gaya harianmu.</p>
                    <div class="home-hero-actions">
                        <a href="{{ route('products.index') }}" class="btn btn-primary btn-lg">Belanja koleksi</a>
                        <a href="{{ route('products.index') }}" class="btn btn-outline btn-lg">Lihat semua produk</a>
                    </div>
                </div>
                <div class="home-hero-media">
                    <img src="{{ asset('images/revolve-hero-shoe.png') }}" alt="Sepatu hitam putih" class="home-hero-product-image">
                </div>
                <div class="home-hero-caption">Pilihan Revolve</div>
            </div>
        </section>
    @endif

    <section class="container-app py-6 lg:py-8">
        <div class="flex items-end justify-between gap-4 mb-4">
            <div>
                <p class="section-kicker">Jelajahi</p>
                <h2 class="text-headline-md">Kategori pilihan</h2>
            </div>
            <a href="{{ route('products.index') }}" class="text-sm font-semibold text-primary-ink hover:text-primary-hover hidden sm:inline">Semua produk</a>
        </div>
        @if ($categories->isNotEmpty())
            <div class="category-rail" aria-label="Kategori produk">
                @foreach ($categories as $cat)
                    <a href="{{ route('categories.show', $cat) }}" class="category-chip">
                        {{ $cat->name }}
                    </a>
                @endforeach
            </div>
        @else
            <p class="text-text-muted">Belum ada kategori.</p>
        @endif
    </section>

    <section class="container-app pb-14" id="discounts">
        <div class="flex items-center justify-between mb-8">
            <div>
                <p class="section-kicker">Harga pilihan</p>
                <h2 class="text-headline-lg">Sedang diskon</h2>
            </div>
                <a href="{{ route('products.index', ['sort' => 'discount']) }}" class="text-sm font-semibold text-primary-ink hover:text-primary-hover hidden sm:inline">Lihat semua</a>
        </div>
        @if ($discountProducts->isNotEmpty())
            <div class="grid-products">
                @foreach ($discountProducts as $product)
                    @include('partials.product-card', ['product' => $product])
                @endforeach
            </div>
        @else
            <div class="card elevation-1 bg-surface py-10 text-center text-text-muted">
                Belum ada produk diskon saat ini. Cek kembali setelah koleksi baru masuk.
            </div>
        @endif
    </section>

    <section class="container-app pb-16">
        <div class="flex items-center justify-between mb-8">
            <div>
                <p class="section-kicker">Baru masuk</p>
                <h2 class="text-headline-lg">Koleksi terbaru</h2>
            </div>
                <a href="{{ route('products.index') }}" class="text-sm font-semibold text-primary-ink hover:text-primary-hover hidden sm:inline">Lihat semua</a>
        </div>
        @if ($latestProducts->isNotEmpty())
            <div class="grid-products">
                @foreach ($latestProducts as $product)
                    @include('partials.product-card', ['product' => $product])
                @endforeach
            </div>
            <div class="text-center mt-10 sm:hidden">
                <a href="{{ route('products.index') }}" class="btn btn-outline">Lihat Semua Produk</a>
            </div>
        @else
            <div class="empty-state card elevation-1 bg-surface">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                    <path d="M2 17l10 5 10-5"/>
                    <path d="M2 12l10 5 10-5"/>
                </svg>
                <h3 class="text-title-md mb-2">Belum ada produk</h3>
                <p>Produk akan segera hadir. Silakan cek kembali nanti.</p>
                <a href="{{ route('products.index') }}" class="btn btn-primary btn-sm">Jelajahi Produk</a>
            </div>
        @endif
    </section>

    <section class="container-app pb-16">
        <div class="seller-callout">
            <div>
                <p class="section-kicker text-white/70">Untuk seller</p>
                <h2 class="text-headline-md text-white mb-2">Bawa koleksimu ke Revolve.</h2>
                <p class="text-white/70 text-sm max-w-lg">Buka toko dan kelola produk fashionmu dari satu tempat.</p>
            </div>
            <div class="shrink-0">
                @auth
                    <a href="{{ route('seller-request.create') }}" class="btn btn-primary btn-lg">Buka toko</a>
                @else
                    <a href="{{ route('register') }}" class="btn btn-primary btn-lg">Daftar sebagai seller</a>
                @endauth
            </div>
        </div>
    </section>
@endsection
