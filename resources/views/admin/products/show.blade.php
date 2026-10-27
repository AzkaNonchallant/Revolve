@extends('layouts.dashboard')

@section('title', 'Detail Produk — Revolve')
@section('header', 'Produk: {{ $product->name }}')

@section('content')
@include('partials.alerts')

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <div class="card elevation-1 bg-surface p-6 lg:p-8">
            <div class="flex flex-col sm:flex-row gap-6">
                <div class="shrink-0">
                    <div class="w-64 max-w-full aspect-square rounded-2xl overflow-hidden bg-surface-subtle">
                        @if ($product->primaryImage?->path)
                            <img src="{{ asset('storage/' . $product->primaryImage->path) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                        @elseif ($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-surface-muted"></div>
                        @endif
                    </div>
                    @if ($product->images->count() > 1)
                        <div class="thumb-grid mt-3">
                            @foreach ($product->images as $img)
                                <img src="{{ asset('storage/' . $img->path) }}" alt="" class="w-full h-full object-cover rounded-lg {{ $img->is_primary ? 'ring-2 ring-primary' : '' }}">
                            @endforeach
                        </div>
                    @endif
                </div>
                <div class="flex-grow">
                    <div class="flex items-center gap-2 flex-wrap mb-2">
                        <span class="badge {{ $product->approval_status === 'approved' ? 'badge-success' : ($product->approval_status === 'pending' ? 'badge-warning' : 'badge-error') }}">{{ $product->approvalStatusLabel() }}</span>
                        <span class="badge {{ $product->status == 1 ? 'badge-primary' : 'badge-muted' }}">{{ $product->status == 1 ? 'Tayang' : 'Nonaktif' }}</span>
                    </div>
                    <h2 class="text-headline-md mb-2">{{ $product->name }}</h2>
                    <p class="text-sm text-text-muted mb-3">{{ $product->category?->name }} · {{ $product->brand?->name ?? 'Tanpa brand' }}</p>
                    <div class="flex items-end gap-3 mb-4">
                        <p class="text-price text-3xl">Rp {{ number_format($product->activePrice(), 0, ',', '.') }}</p>
                        @if ($product->hasDiscount())
                            <p class="text-sm text-text-muted line-through mb-1">Rp {{ number_format((float) $product->price, 0, ',', '.') }}</p>
                        @endif
                    </div>
                    <div class="flex flex-wrap gap-x-6 gap-y-2 text-sm mb-4">
                        <span class="text-text-muted">Stok: <strong class="text-text-primary">{{ $product->totalStock() }}</strong></span>
                        <span class="text-text-muted">Berat: <strong class="text-text-primary">{{ $product->weight }}g</strong></span>
                        <span class="text-text-muted">Bahan: <strong class="text-text-primary">{{ $product->material ?? '—' }}</strong></span>
                    </div>
                    <p class="text-sm text-text-secondary leading-relaxed">{{ $product->description }}</p>
                </div>
            </div>
        </div>

        @if ($product->variants->isNotEmpty())
            <div class="card elevation-1 bg-surface p-6 lg:p-8">
                <h2 class="form-label mb-4">Varian ({{ $product->variants->count() }})</h2>
                <div class="overflow-x-auto">
                    <table class="table-auto w-full text-sm">
                        <thead>
                            <tr class="text-left text-text-muted border-b border-border">
                                <th class="py-3 pr-4 font-medium">Ukuran</th>
                                <th class="py-3 pr-4 font-medium">Warna</th>
                                <th class="py-3 pr-4 font-medium">SKU</th>
                                <th class="py-3 pr-4 font-medium">Harga</th>
                                <th class="py-3 font-medium">Stok</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($product->variants as $variant)
                                <tr class="border-b border-border">
                                    <td class="py-3 pr-4">{{ $variant->size ?? '—' }}</td>
                                    <td class="py-3 pr-4">{{ $variant->color ?? '—' }}</td>
                                    <td class="py-3 pr-4 text-text-muted">{{ $variant->sku ?? '—' }}</td>
                                    <td class="py-3 pr-4">{{ $variant->price ? 'Rp ' . number_format((float) $variant->price, 0, ',', '.') : '—' }}</td>
                                    <td class="py-3">{{ $variant->stock }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <div class="card elevation-1 bg-surface p-6 lg:p-8">
            <h2 class="form-label mb-4">Riwayat Approval</h2>
            @if ($product->approvals->isNotEmpty())
                <div class="space-y-4">
                    @foreach ($product->approvals as $approval)
                        <div class="flex gap-3">
                            <div class="flex flex-col items-center">
                                <span class="w-3 h-3 rounded-full {{ $approval->action === 'approve' ? 'bg-success' : ($approval->action === 'reject' ? 'bg-error' : 'bg-primary') }} mt-1"></span>
                                @unless ($loop->last)
                                    <div class="w-px flex-grow bg-border"></div>
                                @endunless
                            </div>
                            <div class="pb-4">
                                <p class="text-sm font-semibold">
                                    {{ ucfirst($approval->action) }}
                                    @if ($approval->action === 'submit')
                                        oleh Seller
                                    @else
                                        oleh {{ $approval->reviewer?->name ?? 'Admin' }}
                                    @endif
                                </p>
                                <p class="text-xs text-text-muted">{{ $approval->created_at->translatedFormat('d M Y H:i') }}</p>
                                @if ($approval->note)
                                    <p class="text-sm text-text-secondary mt-1">{{ $approval->note }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-text-muted">Belum ada riwayat approval.</p>
            @endif
        </div>
    </div>

    <div class="space-y-6">
        <div class="card elevation-1 bg-surface p-6">
            <h2 class="form-label mb-4">Seller</h2>
            <p class="text-sm font-semibold">{{ $product->seller?->name ?? '—' }}</p>
            <p class="text-sm text-text-secondary">{{ $product->seller?->shop?->name ?? '' }}</p>
            <div class="flex flex-col gap-2 mt-4">
                <a href="{{ route('admin.sellers.show', $product->seller) }}" class="btn btn-outline btn-sm w-full">{{ $product->seller ? 'Detail Seller' : '' }}</a>
                <a href="{{ route('seller.products.index') }}" class="btn btn-outline btn-sm w-full">Buka Dashboard Seller</a>
                <a href="{{ route('products.show', $product) }}" class="btn btn-outline btn-sm w-full">Lihat Halaman Publik</a>
            </div>
        </div>

        <div class="card elevation-1 bg-surface p-6">
            <h2 class="form-label mb-4">Aksi</h2>
            @if ($product->approval_status === 'pending')
                <form action="{{ route('admin.products.approve', $product) }}" method="POST" class="mb-3">
                    @csrf
                    <button type="submit" class="btn btn-primary w-full">Setujui & Tayangkan</button>
                </form>
                <form action="{{ route('admin.products.reject', $product) }}" method="POST">
                    @csrf
                    <div class="space-y-2">
                        <input type="text" name="reason" placeholder="Alasan penolakan..." class="form-input" required>
                        <button type="submit" class="btn btn-outline text-error w-full">Tolak Produk</button>
                    </div>
                </form>
            @else
                <form action="{{ route('admin.products.toggle-active', $product) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn {{ $product->status == 1 ? 'btn-outline' : 'btn-primary' }} w-full">
                        {{ $product->status == 1 ? 'Nonaktifkan Produk' : 'Aktifkan Produk' }}
                    </button>
                </form>
            @endif
            <div class="flex flex-col gap-2 mt-3 pt-3 border-t border-border">
                <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-outline btn-sm w-full">Ubah Data Produk</a>
                <form action="{{ route('admin.products.destroy', $product) }}" method="POST" onsubmit="return confirm('Hapus produk ini permanen?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline text-error btn-sm w-full">Hapus Produk</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection