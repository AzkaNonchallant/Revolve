@extends('layouts.app')

@section('title', 'Produk — Revolve')

@section('content')
<div class="container-app pt-8 lg:pt-12 pb-16">
    <nav class="flex items-center gap-2 text-sm text-text-muted mb-6" aria-label="Breadcrumb">
        <a href="{{ route('home') }}" class="hover:text-text-primary">Beranda</a>
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
        <span class="text-text-primary font-medium">Produk</span>
        @isset($category)
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
            <span class="text-text-primary font-medium">{{ $category->name }}</span>
        @endisset
    </nav>

    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-4 mb-8">
        <div>
            <h1 class="text-headline-lg mb-2">
                @isset($category)
                    {{ $category->name }}
                @else
                    Semua Produk
                @endisset
            </h1>
            <p class="text-text-secondary text-sm">
                {{ $products->total() }} produk tersedia
                @if(request('search'))
                    untuk pencarian &quot;{{ request('search') }}&quot;
                @endif
            </p>
        </div>
        <form action="{{ route('products.index') }}" method="GET" class="flex items-center gap-2 w-full lg:w-auto">
            @if(request('category'))
                <input type="hidden" name="category" value="{{ request('category') }}">
            @endif
            <div class="relative flex-grow lg:w-72">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-text-muted pointer-events-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"/>
                    <path d="M21 21l-4.3-4.3"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari produk..."
                    class="form-input pl-9!" aria-label="Cari produk">
            </div>
            <button type="submit" class="btn btn-secondary shrink-0">Cari</button>
        </form>
    </div>

    @include('partials.alerts')

    {{-- Category chips --}}
    <div class="flex flex-wrap gap-2 mb-8">
        <a href="{{ route('products.index') }}" class="chip {{ !isset($category) && !request('category') ? 'chip-active' : '' }}">Semua</a>
        @foreach ($categories as $cat)
            <a href="{{ route('products.index', ['category' => $cat->id] + (request('search') ? ['search' => request('search')] : [])) }}"
                class="chip {{ (isset($category) && $category->id === $cat->id) || request('category') == $cat->id ? 'chip-active' : '' }}">
                {{ $cat->name }}
            </a>
        @endforeach
    </div>

    @if(request('search'))
        <div class="mb-6 flex items-center gap-2">
            <p class="text-sm text-text-secondary">
                Hasil pencarian untuk &quot;<span class="font-semibold text-text-primary">{{ request('search') }}</span>&quot;
            </p>
            <a href="{{ route('products.index') }}" class="text-sm text-primary font-semibold hover:text-primary-hover">Reset</a>
        </div>
    @endif

    @if ($products->isNotEmpty())
        <div class="grid-products">
            @foreach ($products as $product)
                @include('partials.product-card', ['product' => $product])
            @endforeach
        </div>

        @if ($products->hasPages())
            <div class="mt-12">
                {{ $products->links() }}
            </div>
        @endif
    @else
        <div class="empty-state card elevation-1 bg-surface">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"/>
                <path d="M21 21l-4.3-4.3"/>
            </svg>
            <h3 class="text-title-md mb-2">Produk tidak ditemukan</h3>
            <p>Tidak ada produk yang cocok dengan pencarian Anda.</p>
            <a href="{{ route('products.index') }}" class="btn btn-primary btn-sm">Lihat Semua Produk</a>
        </div>
    @endif
</div>
@endsection