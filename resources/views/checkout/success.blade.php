@extends('layouts.app')

@section('title', 'Pesanan Berhasil — Revolve')

@section('content')
<div class="min-h-[calc(100vh-68px)] flex items-center justify-center py-10">
    <div class="container-app max-w-2xl">
        <div class="card elevation-1 bg-surface p-8 lg:p-12 text-center">
            <div class="w-20 h-20 rounded-full bg-success-bg flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-success" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                    <path d="m9 11 3 3L22 4"/>
                </svg>
            </div>
            <span class="badge badge-success mb-4">Pesanan Berhasil Dibuat</span>
            <h1 class="text-headline-lg mb-3">Terima Kasih, {{ $order->user->name }}!</h1>
            <p class="text-text-secondary mb-8">Pesanan Anda telah kami terima dan sedang menunggu pembayaran. Simpan nomor pesanan berikut untuk memantau status pengiriman.</p>

            <div class="bg-surface-subtle rounded-xl p-6 mb-8">
                <p class="text-label-sm text-text-muted mb-2">Nomor Pesanan</p>
                <p class="text-price text-3xl text-primary">{{ $order->order_number }}</p>
                <div class="divider my-4!"></div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-text-secondary">Total Pembayaran</span>
                    <span class="font-bold">Rp {{ number_format((float) $order->total, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between text-sm mt-2">
                    <span class="text-text-secondary">Jumlah Item</span>
                    <span class="font-bold">{{ $order->items->count() }} produk</span>
                </div>
                <div class="flex items-center justify-between text-sm mt-2">
                    <span class="text-text-secondary">Metode Pembayaran</span>
                    <span class="font-bold capitalize">{{ str_replace('_', ' ', $order->payment_method) }}</span>
                </div>
            </div>

            @include('partials.alerts')

            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a href="{{ route('orders.show', $order) }}" class="btn btn-primary flex-grow sm:flex-grow-0">
                    Lihat Detail Pesanan
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </a>
                <a href="{{ route('products.index') }}" class="btn btn-outline flex-grow sm:flex-grow-0">Lanjut Belanja</a>
            </div>
        </div>
    </div>
</div>
@endsection