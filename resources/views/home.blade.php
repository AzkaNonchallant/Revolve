@extends('layouts.app')

@section('title', 'Revolve | Pilihan Style Harian')

@section('content')
    {{-- Hero: the campaign image is the visual focal point, while the copy stays easy to scan. --}}
    @if ($heroBanners->isNotEmpty())
        <section class="home-hero bg-surface">
            <div data-hero-slider class="relative" role="region" aria-label="Kampanye Revolve" aria-roledescription="carousel">
                @foreach ($heroBanners as $banner)
                    <div class="data-hero-slide {{ $loop->first ? '' : 'hidden' }}" data-hero-slide>
                        <div class="container-app py-10 lg:py-14 grid grid-cols-1 lg:grid-cols-[0.8fr_1.2fr] gap-8 lg:gap-12 items-center">
                            <div class="order-2 lg:order-1">
                                @if ($banner->category)
                                    <p class="text-label-sm text-primary-ink mb-5">{{ $banner->category->name }}</p>
                                @endif
                                <h1 class="text-display-xl max-w-xl mb-5">{!! $banner->title !!}</h1>
                                <p class="max-w-md text-body-lg text-text-secondary mb-8">Pilihan fashion yang mudah dipadukan untuk hari yang terus bergerak.</p>
                                <div class="flex flex-wrap gap-3">
                                    <a href="{{ $banner->link ?: route('products.index') }}" class="btn btn-primary btn-lg">Belanja koleksi</a>
                                    <a href="{{ route('products.index') }}" class="btn btn-outline btn-lg">Lihat semua produk</a>
                                </div>
                            </div>
                            <div class="order-1 lg:order-2 home-hero-media">
                                @if ($banner->image)
                                    <img src="{{ asset('storage/' . $banner->image) }}" alt="{{ $banner->title }}" class="w-full h-full object-cover">
                                @else
                                    <img src="{{ asset('images/revolve-hero-shoe.png') }}" alt="Sepatu Air Jordan hitam putih" class="home-hero-shoe">
                                @endif
                                <div class="home-hero-caption">{{ $banner->category?->name ?: 'Pilihan Revolve' }}</div>
                            </div>
                        </div>
                    </div>
                @endforeach
                @if ($heroBanners->count() > 1)
                    <div class="absolute bottom-6 left-4 lg:left-1/2 lg:-translate-x-1/2 flex items-center gap-2 z-10">
                        @foreach ($heroBanners as $banner)
                            <button type="button" data-hero-dot class="w-11 h-11 flex items-center justify-center" aria-label="Tampilkan slide {{ $loop->iteration }}" aria-pressed="{{ $loop->first ? 'true' : 'false' }}"><span class="home-hero-dot {{ $loop->first ? 'is-active' : '' }}"></span></button>
                        @endforeach
                        <button type="button" data-hero-prev class="hero-slider-control" aria-label="Kampanye sebelumnya">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
                        </button>
                        <button type="button" data-hero-next class="hero-slider-control" aria-label="Kampanye berikutnya">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
                        </button>
                    </div>
                @endif
            </div>
        </section>
    @else
        <section class="home-hero home-hero-fallback">
            <div class="container-app py-12 lg:py-16 grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
                <div>
                    <p class="text-label-sm text-primary-ink mb-5">Revolve fashion</p>
                    <h1 class="text-display-xl max-w-xl mb-5">Temukan gaya yang terasa seperti kamu.</h1>
                    <p class="text-body-lg text-text-secondary max-w-md mb-8">Sepatu, pakaian, dan aksesori dari seller pilihan di satu tempat.</p>
                    <a href="{{ route('products.index') }}" class="btn btn-primary btn-lg">Mulai belanja</a>
                </div>
                <div class="home-hero-media">
                    <img src="{{ asset('images/revolve-hero-shoe.png') }}" alt="Sepatu Air Jordan hitam putih" class="home-hero-shoe">
                </div>
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
