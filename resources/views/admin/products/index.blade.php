@extends('layouts.dashboard')

@section('title', 'Produk — Revolve')
@section('header', 'Kelola Produk')

@section('content')
<div class="card elevation-1 bg-surface p-6 lg:p-8">
    <form method="GET" action="{{ route('admin.products.index') }}" class="flex flex-col sm:flex-row gap-3 mb-6">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari produk..." class="form-input sm:flex-grow">
        <select name="approval" class="form-input sm:w-52">
            <option value="">Semua Approval</option>
            <option value="approved" {{ request('approval') == 'approved' ? 'selected' : '' }}>Disetujui</option>
            <option value="pending" {{ request('approval') == 'pending' ? 'selected' : '' }}>Menunggu Review</option>
            <option value="rejected" {{ request('approval') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
        </select>
        <select name="status" class="form-input sm:w-40">
            <option value="">Semua Status</option>
            <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Aktif</option>
            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Nonaktif</option>
        </select>
        <button type="submit" class="btn btn-outline">Cari</button>
    </form>

    @include('partials.alerts')

    @if ($products->isNotEmpty())
        <div class="overflow-x-auto">
            <table class="table-auto w-full text-sm">
                <thead>
                    <tr class="text-left text-text-muted border-b border-border">
                        <th class="py-3 pr-4 font-medium">Produk</th>
                        <th class="py-3 pr-4 font-medium">Seller</th>
                        <th class="py-3 pr-4 font-medium">Harga</th>
                        <th class="py-3 pr-4 font-medium">Approval</th>
                        <th class="py-3 font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $product)
                        <tr class="border-b border-border">
                            <td class="py-3 pr-4">
                                <a href="{{ route('admin.products.show', $product) }}" class="flex items-center gap-3">
                                    <div class="w-11 h-11 rounded-lg overflow-hidden bg-surface-subtle shrink-0">
                                        @if ($product->primaryImage?->path)
                                            <img src="{{ asset('storage/' . $product->primaryImage->path) }}" alt="" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full bg-surface-muted"></div>
                                        @endif
                                    </div>
                                    <span>
                                        <span class="block font-semibold hover:text-primary max-w-[16rem] truncate">{{ $product->name }}</span>
                                        <span class="block text-xs text-text-muted">{{ $product->category?->name }}</span>
                                    </span>
                                </a>
                            </td>
                            <td class="py-3 pr-4">{{ $product->seller?->shop?->name ?? $product->seller?->name ?? '—' }}</td>
                            <td class="py-3 pr-4">
                                Rp {{ number_format($product->activePrice(), 0, ',', '.') }}
                                @if ($product->hasDiscount())
                                    <span class="text-xs text-text-muted line-through block">Rp {{ number_format((float) $product->price, 0, ',', '.') }}</span>
                                @endif
                            </td>
                            <td class="py-3 pr-4">
                                <span class="badge {{ $product->approval_status === 'approved' ? 'badge-success' : ($product->approval_status === 'pending' ? 'badge-warning' : 'badge-error') }}">{{ $product->approvalStatusLabel() }}</span>
                            </td>
                            <td class="py-3 pr-4">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.products.show', $product) }}" class="btn btn-outline btn-sm">Detail</a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $products->links() }}
        </div>
    @else
        <div class="empty-state"><p>Produk tidak ditemukan.</p></div>
    @endif
</div>
@endsection