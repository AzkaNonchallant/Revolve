@extends('layouts.app')

@section('title', 'Revolve — Belanja Fashion Style Terbaik')

@section('content')
    {{-- Hero Banners --}}
    @if ($heroBanners->isNotEmpty())
        <section class="bg-obsidian text-white relative overflow-hidden">
            <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full opacity-10" style="background: radial-gradient(circle, #FF5400 0%, transparent 70%);"></div>
            <div data-hero-slider class="relative">
                @foreach ($heroBanners as $banner)
                    <div class="data-hero-slide {{ $loop->first ? '' : 'hidden' }}" data-hero-slide>
                        <div class="container-app py-14 lg:py-20 grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-16 items-center">
                            <div>
                                @if ($banner->category)
                                    <span class="badge badge-primary mb-6">{{ $banner->category->name }}</span>
                                @else
                                    <span class="badge badge-primary mb-6">Revolve Fashion</span>
                                @endif
                                <h1 class="text-display-xl mb-6">{!! $banner->title !!}</h1>
                                <div class="flex flex-wrap gap-3">
                                    <a href="{{ $banner->link ?: route('products.index') }}" class="btn btn-primary btn-lg">
                                        Belanja Sekarang
                                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M5 12h14M12 5l7 7-7 7"/>
                                        </svg>
                                    </a>
                                    <a href="{{ route('products.index') }}" class="btn btn-outline btn-lg bg-transparent! border-white/30! text-white! hover:bg-white/10!">
                                        Lihat Produk
                                    </a>
                                </div>
                            </div>
                            @if ($banner->image)
                                <div class="hidden lg:block">
                                    <img src="{{ asset('storage/' . $banner->image) }}" alt="{{ $banner->title }}" class="rounded-2xl w-full max-h-[360px] object-cover shadow-2xl">
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
                @if ($heroBanners->count() > 1)
                    <div class="absolute bottom-6 left-1/2 -translate-x-1/2 flex items-center gap-2 z-10">
                        @foreach ($heroBanners as $banner)
                            <button type="button" data-hero-dot class="w-2 h-2 rounded-full bg-white/40 hover:bg-white/80 transition-colors {{ $loop->first ? 'bg-white!' : '' }}" aria-label="Slide {{ $loop->iteration }}"></button>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>
    @else
        <section class="bg-obsidian text-white">
            <div class="container-app py-14 lg:py-20 relative overflow-hidden">
                <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full opacity-10" style="background: radial-gradient(circle, #FF5400 0%, transparent 70%);"></div>
                <div class="relative">
                    <span class="badge badge-primary mb-6">Revolve Fashion</span>
                    <h1 class="text-display-xl mb-6">
                        Upgrade Gayamu,<br class="hidden lg:block">
                        <span class="text-primary">Tentukan Pilihanmu</span>
                    </h1>
                    <p class="text-lg text-white/70 mb-8 max-w-lg leading-relaxed">
                        Temukan Style yang cocok dengan Anda! Sepatu, baju, celana, hingga jaket dari brand terbaik.
                    </p>
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('products.index') }}" class="btn btn-primary btn-lg">
                            Belanja Sekarang
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M5 12h14M12 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </section>
    @endif

    {{-- Categories --}}
    <section class="container-app py-10 lg:py-14">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-headline-lg">Kategori</h2>
            <a href="{{ route('products.index') }}" class="text-sm font-semibold text-primary hover:text-primary-hover">Semua Produk →</a>
        </div>
        @if ($categories->isNotEmpty())
            <div class="flex flex-wrap gap-2">
                @foreach ($categories as $cat)
                    <a href="{{ route('categories.show', $cat) }}" class="chip">
                        {{ $cat->name }}
                        <span class="ml-1.5 text-xs text-text-muted">{{ $cat->products_count }}</span>
                    </a>
                @endforeach
            </div>
        @else
            <p class="text-text-muted">Belum ada kategori.</p>
        @endif
    </section>

    {{-- Flash Sale / Discount Products --}}
    <section class="container-app pb-14">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-headline-lg">Sedang Diskon</h2>
                <p class="text-sm text-text-muted mt-1">Penawaran terbaik hari ini, buruan sebelum kehabisan!</p>
            </div>
            <a href="{{ route('products.index', ['sort' => 'discount']) }}" class="text-sm font-semibold text-primary hover:text-primary-hover hidden sm:inline">Lihat Semua →</a>
        </div>
        @if ($discountProducts->isNotEmpty())
            <div class="grid-products">
                @foreach ($discountProducts as $product)
                    @include('partials.product-card', ['product' => $product])
                @endforeach
            </div>
        @else
            <div class="card elevation-1 bg-surface py-10 text-center text-text-muted">
                Belum ada produk diskon saat ini.
            </div>
        @endif
    </section>

    {{-- Latest Products --}}
    <section class="container-app pb-16">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-headline-lg">Produk Terbaru</h2>
                <p class="text-sm text-text-muted mt-1">Koleksi terbaru dari para seller Revolve.</p>
            </div>
            <a href="{{ route('products.index') }}" class="text-sm font-semibold text-primary hover:text-primary-hover hidden sm:inline">Lihat Semua →</a>
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

    {{-- CTA Banner --}}
    <section class="container-app pb-16">
        <div class="bg-obsidian rounded-2xl p-8 lg:p-12 flex flex-col lg:flex-row items-center justify-between gap-6 relative overflow-hidden">
            <div class="absolute -top-16 -right-16 w-64 h-64 rounded-full opacity-10" style="background: radial-gradient(circle, #FF5400 0%, transparent 70%);"></div>
            <div class="relative">
                <span class="badge badge-primary mb-3">Jualan di Revolve</span>
                <h2 class="text-headline-md text-white mb-2">Jadilah Seller dan Jualan Fashion</h2>
                <p class="text-white/60 text-sm">Daftarkan tokomu di Revolve, kelola produk, dan raih pelanggan lebih banyak.</p>
            </div>
            <div class="relative shrink-0">
                @auth
                    <a href="{{ route('seller-request.create') }}" class="btn btn-primary btn-lg">Buka Toko Sekarang</a>
                @else
                    <a href="{{ route('register') }}" class="btn btn-primary btn-lg">Daftar Sekarang</a>
                @endauth
            </div>
        </div>
    </section>
@endsection

@push('scripts')
@if ($heroBanners->count() > 1)
<script>
document.addEventListener('DOMContentLoaded', () => {
    const slides = [...document.querySelectorAll('[data-hero-slide]')];
    const dots = [...document.querySelectorAll('[data-hero-dot]')];
    if (slides.length < 2) return;
    let current = 0;
    let timer;

    const show = (index) => {
        slides[current].classList.add('hidden');
        dots[current]?.classList.remove('bg-white!');
        current = (index + slides.length) % slides.length;
        slides[current].classList.remove('hidden');
        dots[current]?.classList.add('bg-white!');
    };

    dots.forEach((dot, i) => dot.addEventListener('click', () => { show(i); restart(); }));

    const next = () => show(current + 1);
    const restart = () => { clearInterval(timer); timer = setInterval(next, 5000); };
    restart();
});
</script>
@endif
@endpush