@extends('layouts.dashboard')

@section('title', 'Detail Pengajuan Seller — Revolve')
@section('header', 'Pengajuan Seller')

@section('content')
@include('partials.alerts')

<div class="max-w-3xl">
    <div class="card elevation-1 bg-surface p-6 lg:p-8 mb-6">
        <div class="flex items-center justify-between gap-4 mb-6">
            <div class="flex items-center gap-4">
                @if ($sellerRequest->logo)
                    <img src="{{ asset('storage/' . $sellerRequest->logo) }}" alt="" class="w-16 h-16 rounded-2xl object-cover">
                @else
                    <span class="w-16 h-16 rounded-2xl bg-primary/10 text-primary text-2xl font-bold flex items-center justify-center">{{ strtoupper(substr($sellerRequest->shop_name, 0, 1)) }}</span>
                @endif
                <div>
                    <h2 class="text-headline-sm">{{ $sellerRequest->shop_name }}</h2>
                    <span class="badge {{ $sellerRequest->status === 'pending' ? 'badge-warning' : ($sellerRequest->status === 'approved' ? 'badge-success' : 'badge-error') }}">{{ \App\Models\SellerRequest::statusLabel($sellerRequest->status) }}</span>
                </div>
            </div>
        </div>

        <dl class="space-y-4 text-sm">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <dt class="text-text-muted">Pemohon</dt>
                    <dd class="font-semibold">{{ $sellerRequest->user?->name }}</dd>
                </div>
                <div>
                    <dt class="text-text-muted">Email</dt>
                    <dd class="font-semibold">{{ $sellerRequest->user?->email }}</dd>
                </div>
                <div>
                    <dt class="text-text-muted">Telepon</dt>
                    <dd>{{ $sellerRequest->phone }}</dd>
                </div>
                <div>
                    <dt class="text-text-muted">Kategori</dt>
                    <dd>{{ $sellerRequest->category ?? '—' }}</dd>
                </div>
            </div>
            <div>
                <dt class="text-text-muted">Alamat Toko</dt>
                <dd>{{ $sellerRequest->address }}</dd>
            </div>
            @if ($sellerRequest->reason)
                <div>
                    <dt class="text-text-muted">Alasan</dt>
                    <dd>{{ $sellerRequest->reason }}</dd>
                </div>
            @endif
            @if ($sellerRequest->shop_description)
                <div>
                    <dt class="text-text-muted">Deskripsi Toko</dt>
                    <dd>{{ $sellerRequest->shop_description }}</dd>
                </div>
            @endif
            @if ($sellerRequest->admin_note)
                <div>
                    <dt class="text-text-muted">Catatan Admin</dt>
                    <dd>{{ $sellerRequest->admin_note }}</dd>
                </div>
            @endif
        </dl>
    </div>

    @if ($sellerRequest->status === 'pending')
        <div class="card elevation-1 bg-surface p-6">
            <div class="flex flex-col sm:flex-row gap-3">
                <form action="{{ route('admin.seller-requests.approve', $sellerRequest) }}" method="POST" class="flex-grow">
                    @csrf
                    <button type="submit" class="btn btn-primary w-full">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                        Setujui & Upgrade ke Seller
                    </button>
                </form>
                <form action="{{ route('admin.seller-requests.reject', $sellerRequest) }}" method="POST">
                    @csrf
                    <div class="flex flex-col sm:flex-row gap-2">
                        <input type="text" name="reason" placeholder="Alasan penolakan..." class="form-input" required>
                        <button type="submit" class="btn btn-outline text-error shrink-0" onclick="return confirm('Tolak pengajuan ini?')">Tolak</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
@endsection