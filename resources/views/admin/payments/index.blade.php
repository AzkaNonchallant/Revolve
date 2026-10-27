@extends('layouts.dashboard')

@section('title', 'Pembayaran — Revolve')
@section('header', 'Kelola Pembayaran')

@section('content')
<div class="card elevation-1 bg-surface p-6 lg:p-8">
    <form method="GET" action="{{ route('admin.payments.index') }}" class="flex flex-col sm:flex-row gap-3 mb-6">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari transaksi..." class="form-input sm:w-64">
        <select name="status" class="form-input sm:w-48">
            <option value="">Semua Status</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu</option>
            <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Dibayar</option>
            <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Gagal</option>
            <option value="refunded" {{ request('status') == 'refunded' ? 'selected' : '' }}>Refund</option>
        </select>
        <button type="submit" class="btn btn-outline">Cari</button>
    </form>

    @include('partials.alerts')

    @if ($payments->isNotEmpty())
        <div class="overflow-x-auto">
            <table class="table-auto w-full text-sm">
                <thead>
                    <tr class="text-left text-text-muted border-b border-border">
                        <th class="py-3 pr-4 font-medium">Transaksi</th>
                        <th class="py-3 pr-4 font-medium">Pesanan</th>
                        <th class="py-3 pr-4 font-medium">Pembeli</th>
                        <th class="py-3 pr-4 font-medium">Jumlah</th>
                        <th class="py-3 pr-4 font-medium">Status</th>
                        <th class="py-3 font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($payments as $payment)
                        <tr class="border-b border-border">
                            <td class="py-3 pr-4">
                                <p class="font-semibold text-xs">{{ $payment->transaction_id }}</p>
                                <p class="text-xs text-text-muted">{{ $payment->payment_method }}</p>
                            </td>
                            <td class="py-3 pr-4">
                                @if ($payment->order)
                                    <a href="{{ route('admin.orders.show', $payment->order) }}" class="font-semibold hover:text-primary">#{{ $payment->order->order_number }}</a>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="py-3 pr-4">{{ $payment->order?->user?->name ?? '—' }}</td>
                            <td class="py-3 pr-4">Rp {{ number_format((float) $payment->amount, 0, ',', '.') }}</td>
                            <td class="py-3 pr-4">
                                <span class="badge {{ $payment->payment_status === 'paid' ? 'badge-success' : ($payment->payment_status === 'failed' || $payment->payment_status === 'refunded' ? 'badge-error' : 'badge-warning') }}">{{ $payment->statusLabel() }}</span>
                            </td>
                            <td class="py-3 pr-4">
                                @if ($payment->payment_status === 'paid')
                                    <form action="{{ route('admin.payments.refund', $payment) }}" method="POST" onsubmit="return confirm('Refund pembayaran ini?')">
                                        @csrf
                                        <button type="submit" class="btn btn-outline text-error btn-sm">Refund</button>
                                    </form>
                                @else
                                    <span class="text-xs text-text-muted">—</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $payments->links() }}
        </div>
    @else
        <div class="empty-state"><p>Belum ada pembayaran.</p></div>
    @endif
</div>
@endsection