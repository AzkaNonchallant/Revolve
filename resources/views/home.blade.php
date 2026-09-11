@extends('layouts.app')

@section('title', 'Revolve — Belanja Produk Kesehatan Pilihan')

@section('content')
    {{-- Hero --}}
    <section class="bg-obsidian text-white">
        <div class="container-app py-14 lg:py-20 relative overflow-hidden">
            <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full opacity-10" style="background: radial-gradient(circle, #FF5400 0%, transparent 70%);"></div>
            <div class="absolute -bottom-32 -left-24 w-80 h-80 rounded-full opacity-5" style="background: radial-gradient(circle, #FF5400 0%, transparent 70%);"></div>
            <div class="relative grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-16 items-center">
                <div>
                    <span class="badge badge-primary mb-6"></span>
                    <h1 class="text-display-xl mb-6">
                        Upgrade Gayamu,<br class="hidden lg:block">
                        <span class="text-primary">Tentukan Pilihanmu</span>
                    </h1>
                    <p class="text-lg text-white/70 mb-8 max-w-lg leading-relaxed">
                        Temukan Style yang cocok dengan Anda!
                    </p>
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('products.index') }}" class="btn btn-primary btn-lg">
                            Belanja Sekarang
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M5 12h14M12 5l7 7-7 7"/>
                            </svg>
                        </a>
                        @auth
                            <a href="{{ route('orders.index') }}" class="btn btn-outline btn-lg bg-transparent! border-white/30! text-white! hover:bg-white/10!">
                                Lihat Pesanan
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="btn btn-outline btn-lg bg-transparent! border-white/30! text-white! hover:bg-white/10!">
                                Daftar Gratis
                            </a>
                        @endauth
                    </div>
                    <div class="flex items-center gap-8 mt-10">
                        <div>
                            <p class="text-headline-md font-bold text-primary">100%</p>
                            <p class="text-xs text-white/60 mt-1">Produk Terkurasi</p>
                        </div>
                        <div class="w-px h-10 bg-white/15"></div>
                        <div>
                            <p class="text-headline-md font-bold text-primary">{{ $categories->count() }}</p>
                            <p class="text-xs text-white/60 mt-1">Kategori Produk</p>
                        </div>
                        <div class="w-px h-10 bg-white/15"></div>
                        <div>
                            <p class="text-headline-md font-bold text-primary">{{ $featuredProducts->count() }}+</p>
                            <p class="text-xs text-white/60 mt-1">Produk Pilihan</p>
                        </div>
                    </div>
                </div>
                <div class="hidden lg:block">
                    <div class="rounded-2xl bg-white/5 border border-white/10 p-8 text-center">
                        <svg class="w-24 h-24 mx-auto mb-6 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                            <path d="M2 17l10 5 10-5"/>
                            <path d="M2 12l10 5 10-5"/>
                        </svg>
                        <h2 class="text-headline-md mb-2">Revolve</h2>
                        <p class="text-white/60 text-sm">Belanja cerdas untuk tubuh yang lebih sehat dan berenergi.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

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

    {{-- Featured Products --}}
    <section class="container-app pb-16">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-headline-lg">Produk Pilihan</h2>
            <a href="{{ route('products.index') }}" class="text-sm font-semibold text-primary hover:text-primary-hover hidden sm:inline">Lihat Semua →</a>
        </div>
        @if ($featuredProducts->isNotEmpty())
            <div class="grid-products">
                @foreach ($featuredProducts as $product)
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
                <span class="badge badge-primary mb-3">Promo Kesehatan</span>
                <h2 class="text-headline-md text-white mb-2">Raih Tubuh Sehat dengan Revolve</h2>
                <p class="text-white/60 text-sm">Daftar sekarang dan mulai belanja produk kesehatan terbaik untuk keluarga Anda.</p>
            </div>
            <div class="relative shrink-0">
                @auth
                    <a href="{{ route('products.index') }}" class="btn btn-primary btn-lg">Mulai Belanja</a>
                @else
                    <a href="{{ route('register') }}" class="btn btn-primary btn-lg">Daftar Sekarang</a>
                @endauth
            </div>
        </div>
    </section>
@endsection