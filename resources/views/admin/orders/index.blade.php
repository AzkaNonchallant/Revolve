@extends('layouts.dashboard')

@section('title', 'Pesanan — Revolve')
@section('header', 'Kelola Pesanan')

@section('content')
<div class="card elevation-1 bg-surface p-6 lg:p-8">
    <form method="GET" action="{{ route('admin.orders.index') }}" class="flex flex-col sm:flex-row gap-3 mb-6">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor pesanan..." class="form-input sm:w-64">
        <select name="status" class="form-input sm:w-56">
            <option value="">Semua Status</option>
            @foreach (\App\Models\Order::STATUS_MAP as $value => $label)
                <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-outline">Cari</button>
    </form>

    @include('partials.alerts')

    @if ($orders->isNotEmpty())
        <div class="overflow-x-auto">
            <table class="table-auto w-full text-sm">
                <thead>
                    <tr class="text-left text-text-muted border-b border-border">
                        <th class="py-3 pr-4 font-medium">Pesanan</th>
                        <th class="py-3 pr-4 font-medium">Pembeli</th>
                        <th class="py-3 pr-4 font-medium">Total</th>
                        <th class="py-3 pr-4 font-medium">Status</th>
                        <th class="py-3 font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $order)
                        <tr class="border-b border-border">
                            <td class="py-3 pr-4">
                                <a href="{{ route('admin.orders.show', $order) }}" class="font-semibold hover:text-primary">#{{ $order->order_number }}</a>
                                <p class="text-xs text-text-muted">{{ $order->created_at->translatedFormat('d M Y H:i') }}</p>
                            </td>
                            <td class="py-3 pr-4">{{ $order->user?->name ?? '—' }}</td>
                            <td class="py-3 pr-4">Rp {{ number_format((float) $order->total, 0, ',', '.') }}</td>
                            <td class="py-3 pr-4"><span class="badge badge-outline">{{ $order->statusLabel() }}</span></td>
                            <td class="py-3 pr-4">
                                <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-outline btn-sm">Detail</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $orders->links() }}
        </div>
    @else
        <div class="empty-state"><p>Pesanan tidak ditemukan.</p></div>
    @endif
</div>
@endsection