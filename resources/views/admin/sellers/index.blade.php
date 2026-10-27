@extends('layouts.dashboard')

@section('title', 'Seller — Revolve')
@section('header', 'Daftar Seller')

@section('content')
<div class="card elevation-1 bg-surface p-6 lg:p-8">
    <form method="GET" action="{{ route('admin.sellers.index') }}" class="flex flex-col sm:flex-row gap-3 mb-6">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / email / toko..." class="form-input sm:flex-grow">
        <button type="submit" class="btn btn-outline">Cari</button>
    </form>

    @include('partials.alerts')

    @if ($sellers->isNotEmpty())
        <div class="overflow-x-auto">
            <table class="table-auto w-full text-sm">
                <thead>
                    <tr class="text-left text-text-muted border-b border-border">
                        <th class="py-3 pr-4 font-medium">Seller</th>
                        <th class="py-3 pr-4 font-medium">Toko</th>
                        <th class="py-3 pr-4 font-medium">Produk Aktif</th>
                        <th class="py-3 pr-4 font-medium">Status</th>
                        <th class="py-3 font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sellers as $seller)
                        <tr class="border-b border-border">
                            <td class="py-3 pr-4">
                                <a href="{{ route('admin.sellers.show', $seller) }}" class="flex items-center gap-3">
                                    <span class="w-9 h-9 rounded-full bg-primary/10 text-primary text-xs font-bold flex items-center justify-center shrink-0">{{ strtoupper(substr($seller->name, 0, 1)) }}</span>
                                    <span>
                                        <span class="block font-semibold hover:text-primary">{{ $seller->name }}</span>
                                        <span class="block text-xs text-text-muted">{{ $seller->email }}</span>
                                    </span>
                                </a>
                            </td>
                            <td class="py-3 pr-4">{{ $seller->shop?->name ?? '—' }}</td>
                            <td class="py-3 pr-4">{{ $seller->products_count }}</td>
                            <td class="py-3 pr-4">
                                <span class="badge {{ $seller->is_active ? 'badge-success' : 'badge-error' }}">{{ $seller->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                            </td>
                            <td class="py-3 pr-4">
                                <a href="{{ route('admin.sellers.show', $seller) }}" class="btn btn-outline btn-sm">Detail</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $sellers->links() }}
        </div>
    @else
        <div class="empty-state"><p>Belum ada seller.</p></div>
    @endif
</div>
@endsection