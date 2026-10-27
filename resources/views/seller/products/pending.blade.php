@extends('layouts.dashboard')

@section('title', 'Produk Menunggu Review — Revolve')
@section('header', 'Produk Menunggu Review')

@section('content')
<p class="text-sm text-text-muted mb-6">Produk di bawah ini sedang direview oleh Admin.</p>

@include('partials.alerts')

<div class="card elevation-1 bg-surface p-6 lg:p-8">
    @if ($products->isNotEmpty())
        <div class="space-y-4">
            @foreach ($products as $product)
                <div class="flex items-center gap-4 p-4 rounded-xl border border-border">
                    <div class="w-16 h-16 rounded-lg overflow-hidden bg-surface-subtle shrink-0">
                        @if ($product->primaryImage?->path)
                            <img src="{{ asset('storage/' . $product->primaryImage->path) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-surface-muted"></div>
                        @endif
                    </div>
                    <div class="flex-grow min-w-0">
                        <p class="font-semibold text-sm truncate">{{ $product->name }}</p>
                        <p class="text-xs text-text-muted mt-1">Diajukan: {{ $product->created_at->translatedFormat('d M Y H:i') }}</p>
                    </div>
                    <span class="badge badge-warning shrink-0">Menunggu Review</span>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $products->links() }}
        </div>
    @else
        <div class="empty-state">
            <p>Tidak ada produk yang menunggu review.</p>
        </div>
    @endif
</div>
@endsection