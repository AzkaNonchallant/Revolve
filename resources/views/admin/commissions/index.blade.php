@extends('layouts.dashboard')

@section('title', 'Komisi — Revolve')
@section('header', 'Kelola Komisi')

@section('content')
@include('partials.alerts')

<div class="card elevation-1 bg-surface p-6 lg:p-8">
    @if ($commissions->isNotEmpty())
        <div class="overflow-x-auto">
            <table class="table-auto w-full text-sm">
                <thead>
                    <tr class="text-left text-text-muted border-b border-border">
                        <th class="py-3 pr-4 font-medium">Seller</th>
                        <th class="py-3 pr-4 font-medium">Pesanan</th>
                        <th class="py-3 pr-4 font-medium">Item</th>
                        <th class="py-3 pr-4 font-medium">Subtotal</th>
                        <th class="py-3 pr-4 font-medium">Komisi</th>
                        <th class="py-3 pr-4 font-medium">Bersih</th>
                        <th class="py-3 pr-4 font-medium">Status</th>
                        <th class="py-3 font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($commissions as $commission)
                        <tr class="border-b border-border">
                            <td class="py-3 pr-4">
                                <p class="font-semibold">{{ $commission->seller?->shop?->name ?? $commission->seller?->name ?? '—' }}</p>
                                <p class="text-xs text-text-muted">{{ $commission->commission_rate }}%</p>
                            </td>
                            <td class="py-3 pr-4">
                                @if ($commission->order)
                                    <a href="{{ route('admin.orders.show', $commission->order) }}" class="font-semibold hover:text-primary">#{{ $commission->order->order_number }}</a>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="py-3 pr-4 truncate max-w-[10rem]">{{ $commission->orderItem?->product?->name ?? '—' }}</td>
                            <td class="py-3 pr-4">Rp {{ number_format((float) $commission->subtotal, 0, ',', '.') }}</td>
                            <td class="py-3 pr-4 text-warning">- Rp {{ number_format((float) $commission->commission_amount, 0, ',', '.') }}</td>
                            <td class="py-3 pr-4 text-success font-semibold">Rp {{ number_format((float) $commission->net_amount, 0, ',', '.') }}</td>
                            <td class="py-3 pr-4">
                                <span class="badge {{ $commission->status === 'paid' ? 'badge-success' : 'badge-warning' }}">{{ $commission->status === 'paid' ? 'Dibayar' : 'Menunggu' }}</span>
                            </td>
                            <td class="py-3 pr-4">
                                @if ($commission->status === 'pending')
                                    <form action="{{ route('admin.commissions.pay', $commission) }}" method="POST" onsubmit="return confirm('Tandai komisi ini sudah dibayarkan?')">
                                        @csrf
                                        <button type="submit" class="btn btn-outline btn-sm">Tandai Dibayar</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $commissions->links() }}
        </div>
    @else
        <div class="empty-state"><p>Belum ada komisi.</p></div>
    @endif
</div>
@endsection