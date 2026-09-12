@extends('layouts.dashboard')

@section('title', 'Produk Saya — Revolve')
@section('header', 'Produk Saya')

@section('content')
<a href="{{ route('seller.products.create') }}" class="btn btn-primary mb-6">
    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
    Tambah Produk
</a>

<div class="card elevation-1 bg-surface p-6 lg:p-8">
    <form method="GET" action="{{ route('seller.products.index') }}" class="flex flex-col sm:flex-row gap-3 mb-6">
        <select name="status" class="form-input sm:w-48">
            <option value="">Semua Status</option>
            <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Aktif</option>
            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Nonaktif</option>
        </select>
        <select name="approval" class="form-input sm:w-56">
            <option value="">Semua Approval</option>
            <option value="approved" {{ request('approval') == 'approved' ? 'selected' : '' }}>Disetujui</option>
            <option value="pending" {{ request('approval') == 'pending' ? 'selected' : '' }}>Menunggu Review</option>
            <option value="rejected" {{ request('approval') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
        </select>
        <button type="submit" class="btn btn-outline">Filter</button>
        @if (request('status') !== null || request('approval') !== '')
            <a href="{{ route('seller.products.index') }}" class="btn btn-outline btn-sm self-center">Reset</a>
        @endif
    </form>

    @include('partials.alerts')

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
                        <div class="flex items-center gap-2 flex-wrap">
                            <p class="font-semibold text-sm truncate">{{ $product->name }}</p>
                            @if ($product->approval_status === 'pending')
                                <span class="badge badge-warning">Menunggu Review</span>
                            @elseif ($product->approval_status === 'rejected')
                                <span class="badge badge-error">Ditolak</span>
                            @else
                                <span class="badge badge-success">Disetujui</span>
                            @endif
                            <span class="badge {{ $product->status == 1 ? 'badge-primary' : 'badge-outline' }}">{{ $product->status == 1 ? 'Aktif' : 'Nonaktif' }}</span>
                        </div>
                        <p class="text-xs text-text-muted mt-1 truncate">{{ $product->category?->name }} · Stok {{ $product->totalStock() }} · Terjual {{ $product->soldCount() }}</p>
                        <p class="text-sm font-bold text-primary mt-1">Rp {{ number_format($product->activePrice(), 0, ',', '.') }}</p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-2 shrink-0">
                        @if ($product->approval_status === 'rejected' || ($product->approval_status === 'pending' && ! $product->isActive()))
                            <form action="{{ route('seller.products.submit', $product) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-outline btn-sm">Ajukan</button>
                            </form>
                        @endif
                        <a href="{{ route('seller.products.edit', $product) }}" class="btn btn-outline btn-sm">Ubah</a>
                        <form action="{{ route('seller.products.destroy', $product) }}" method="POST" onsubmit="return confirm('Hapus produk ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline btn-sm text-error">Hapus</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $products->links() }}
        </div>
    @else
        <div class="empty-state">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="w-12 h-12"><path d="M20 7 8 3 4 4v8l12 4 4-1v-8ZM3 3v11M20 12v8"/></svg>
            <p class="mt-3">Belum ada produk.</p>
            <a href="{{ route('seller.products.create') }}" class="btn btn-primary mt-4">Tambah Produk Pertama</a>
        </div>
    @endif
</div>
@endsection