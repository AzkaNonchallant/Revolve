@extends('layouts.app')

@section('title', $shop->name . ' — Revolve')

@section('content')
<div class="container-app pt-8 lg:pt-12 pb-16">
    <nav class="flex items-center gap-2 text-sm text-text-muted mb-6" aria-label="Breadcrumb">
        <a href="{{ route('home') }}" class="hover:text-text-primary">Beranda</a>
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
        <a href="{{ route('products.index') }}" class="hover:text-text-primary">Produk</a>
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
        <span class="text-text-primary font-medium">{{ $shop->name }}</span>
    </nav>

    <div class="card elevation-1 bg-surface p-6 mb-10">
        <div class="flex flex-col sm:flex-row sm:items-center gap-5">
            @if ($shop->logo)
                <img src="{{ asset('storage/' . $shop->logo) }}" alt="{{ $shop->name }}" class="w-20 h-20 rounded-2xl object-cover shrink-0">
            @else
                <span class="w-20 h-20 rounded-2xl bg-primary/10 text-primary text-3xl font-bold flex items-center justify-center shrink-0">{{ strtoupper(substr($shop->name, 0, 1)) }}</span>
            @endif
            <div class="flex-grow min-w-0">
                <div class="flex items-center gap-2 flex-wrap">
                    <h1 class="text-headline-lg">{{ $shop->name }}</h1>
                    <span class="badge badge-{{ $shop->is_verified ? 'primary' : 'muted' }}">{{ $shop->is_verified ? 'Terverifikasi' : 'Toko' }}</span>
                </div>
                @if ($shop->description)
                    <p class="text-sm text-text-secondary mt-2">{{ $shop->description }}</p>
                @endif
                <div class="flex items-center gap-4 mt-3 text-sm text-text-muted">
                    <span>{{ $products->total() }} produk</span>
                    <span>{{ $shop->owner?->name }}</span>
                </div>
            </div>
        </div>
    </div>

    @include('partials.alerts')

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
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="w-10 h-10">
                <path d="M3 9l1-5h16l1 5"/>
                <path d="M3 9a3 3 0 0 0 6 0 3 3 0 0 0 6 0 3 3 0 0 0 6 0"/>
                <path d="M5 12v7h14v-7"/>
            </svg>
            <h3 class="text-title-md mb-2">Belum ada produk</h3>
            <p>Tidak ada produk dari toko ini untuk saat ini.</p>
            <a href="{{ route('products.index') }}" class="btn btn-primary btn-sm">Lihat Semua Produk</a>
        </div>
    @endif
</div>
@endsection