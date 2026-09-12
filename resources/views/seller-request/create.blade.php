@extends('layouts.app')

@section('title', 'Ajukan Menjadi Seller — Revolve')

@section('content')
<div class="container-app pt-8 lg:pt-12 pb-16">
    <nav class="flex items-center gap-2 text-sm text-text-muted mb-6" aria-label="Breadcrumb">
        <a href="{{ route('home') }}" class="hover:text-text-primary">Beranda</a>
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
        <a href="{{ route('seller-request.index') }}" class="hover:text-text-primary">Pengajuan Seller</a>
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
        <span class="text-text-primary font-medium">Ajukan</span>
    </nav>

    @include('partials.alerts')

    <div class="max-w-2xl">
        <div class="card elevation-1 bg-surface p-6 lg:p-8 mb-6 bg-primary/5 border-primary/20">
            <h1 class="text-headline-sm mb-2">Jualan di Revolve</h1>
            <p class="text-sm text-text-secondary mb-4">
                Isi form berikut untuk mengajukan menjadi Seller. Admin akan mereview pengajuan Anda. Setelah disetujui, Anda dapat membuat toko, mengunggah produk, dan mulai menjual.
            </p>
            <ul class="text-sm text-text-secondary space-y-1.5">
                <li>· Buat toko dengan nama dan logo</li>
                <li>· Unggah produk dengan varian (warna & ukuran)</li>
                <li>· Kelola pesanan dan pantau penjualan</li>
                <li>· Komisi marketplace: <span class="font-semibold text-text-primary">{{ \App\Models\Setting::get('commission_rate', 5) }}%</span> per pesanan</li>
            </ul>
        </div>

        @if ($latest?->status === 'pending')
            <div class="card elevation-1 bg-surface p-6 text-center">
                <p class="text-text-secondary mb-2">Anda sudah memiliki pengajuan yang sedang direview Admin.</p>
                <a href="{{ route('seller-request.index') }}" class="btn btn-outline">Lihat Pengajuan</a>
            </div>
        @elseif ($latest?->status === 'rejected')
            <div class="card elevation-1 bg-surface p-4 mb-6 bg-error-bg">
                <p class="text-sm text-error font-semibold">Pengajuan terakhir Anda ditolak. Silakan ajukan ulang dengan memperbaiki data.</p>
            </div>
            <form action="{{ route('seller-request.store') }}" method="POST" enctype="multipart/form-data" class="card elevation-1 bg-surface p-6 lg:p-8 space-y-5">
                @csrf
                @include('partials.seller-request-form', ['categories' => $categories])
            </form>
        @else
            <form action="{{ route('seller-request.store') }}" method="POST" enctype="multipart/form-data" class="card elevation-1 bg-surface p-6 lg:p-8 space-y-5">
                @csrf
                @include('partials.seller-request-form', ['categories' => $categories])
            </form>
        @endif
    </div>
</div>
@endsection