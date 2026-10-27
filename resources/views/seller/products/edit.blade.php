@extends('layouts.dashboard')

@section('title', 'Ubah Produk — Revolve')
@section('header', 'Ubah Produk')

@section('content')
<a href="{{ route('seller.products.index') }}" class="inline-flex items-center gap-2 text-sm text-text-secondary hover:text-text-primary mb-6">
    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
    Kembali ke Produk Saya
</a>

@if ($product->approval_status === 'pending')
    <div class="p-4 rounded-xl bg-warning/10 border border-warning/30 text-sm text-text-primary mb-6">
        Produk ini sedang dalam review admin. Mengubah data penting akan mengirim produk untuk review ulang.
    </div>
@elseif ($product->approval_status === 'rejected')
    <div class="p-4 rounded-xl bg-error/10 border border-error/30 text-sm text-text-primary mb-6">
        Produk ditolak oleh admin: <strong>{{ $product->approval_note ?? 'Tidak ada catatan' }}</strong>. Silakan perbaiki lalu ajukan ulang.
    </div>
@endif

<form action="{{ route('seller.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="card elevation-1 bg-surface p-6 lg:p-8">
    @csrf
    @method('PUT')
    @include('partials.alerts')
    @include('seller.products._form', ['product' => $product])

    <div class="flex flex-col sm:flex-row gap-3 mt-8 pt-6 border-t border-border">
        <button type="submit" class="btn btn-primary flex-grow sm:flex-grow-0">
            Simpan Perubahan
        </button>
        <a href="{{ route('seller.products.index') }}" class="btn btn-outline">Batal</a>
    </div>
</form>
@endsection