@extends('layouts.app')

@section('title', 'Pengajuan Seller — Revolve')

@section('content')
<div class="container-app pt-8 lg:pt-12 pb-16">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <h1 class="text-headline-lg">Pengajuan Menjadi Seller</h1>
        <a href="{{ route('seller-request.create') }}" class="btn btn-primary btn-sm">Ajukan Baru</a>
    </div>

    @include('partials.alerts')

    @if ($requests->isNotEmpty())
        <div class="space-y-4">
            @foreach ($requests as $request)
                <div class="card elevation-1 bg-surface p-5 lg:p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-center gap-4">
                            @if ($request->logo)
                                <img src="{{ asset('storage/' . $request->logo) }}" alt="{{ $request->shop_name }}" class="w-14 h-14 rounded-xl object-cover">
                            @else
                                <span class="w-14 h-14 rounded-xl bg-obsidian text-white text-lg font-bold flex items-center justify-center">{{ strtoupper(substr($request->shop_name, 0, 1)) }}</span>
                            @endif
                            <div>
                                <p class="text-title-md font-semibold">{{ $request->shop_name }}</p>
                                <p class="text-sm text-text-secondary mt-0.5">Diajukan {{ $request->created_at->translatedFormat('d M Y, H:i') }}</p>
                            </div>
                        </div>
                        <div class="flex items-center justify-between sm:justify-end gap-3">
                            <span class="badge {{ $request->status === 'approved' ? 'badge-success' : ($request->status === 'rejected' ? 'badge-error' : 'badge-warning') }}">
                                {{ \App\Models\SellerRequest::statusLabel($request->status) }}
                            </span>
                            @if ($request->status === 'approved')
                                @if ($request->user?->shop)
                                    <a href="{{ route('shops.show', $request->user->shop) }}" class="btn btn-outline btn-sm">Lihat Toko</a>
                                @else
                                    <a href="{{ route('seller.dashboard') }}" class="btn btn-primary btn-sm">Buka Dashboard</a>
                                @endif
                            @endif
                        </div>
                    </div>
                    @if ($request->status === 'rejected' && $request->admin_note)
                        <div class="mt-4 p-4 bg-error-bg rounded-xl text-sm">
                            <p class="font-semibold text-error mb-1">Alasan penolakan</p>
                            <p class="text-text-secondary">{{ $request->admin_note }}</p>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        @if ($requests->hasPages())
            <div class="mt-10">
                {{ $requests->links() }}
            </div>
        @endif
    @else
        <div class="empty-state card elevation-1 bg-surface">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 9l1-5h16l1 5"/><path d="M3 9a3 3 0 0 0 6 0 3 3 0 0 0 6 0 3 3 0 0 0 6 0"/><path d="M5 12v7h14v-7"/>
            </svg>
            <h3 class="text-title-md mb-2">Belum ada pengajuan</h3>
            <p>Mulai jualan di Revolve dengan mengajukan permintaan menjadi Seller.</p>
            <a href="{{ route('seller-request.create') }}" class="btn btn-primary">Ajukan Sekarang</a>
        </div>
    @endif
</div>
@endsection