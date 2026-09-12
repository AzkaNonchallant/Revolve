@extends('layouts.dashboard')

@section('title', 'Tambah Produk — Revolve')
@section('header', 'Tambah Produk Baru')

@section('content')
<a href="{{ route('seller.products.index') }}" class="inline-flex items-center gap-2 text-sm text-text-secondary hover:text-text-primary mb-6">
    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
    Kembali ke Produk Saya
</a>

<form action="{{ route('seller.products.store') }}" method="POST" enctype="multipart/form-data" class="card elevation-1 bg-surface p-6 lg:p-8">
    @csrf
    @include('partials.alerts')
    @include('seller.products._form')

    <div class="flex flex-col sm:flex-row gap-3 mt-8 pt-6 border-t border-border">
        <button type="submit" class="btn btn-primary flex-grow sm:flex-grow-0">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 12 2 2 4-4"/><circle cx="12" cy="12" r="10"/></svg>
            Simpan & Ajukan Produk
        </button>
        <a href="{{ route('seller.products.index') }}" class="btn btn-outline">Batal</a>
    </div>
</form>
@endsection