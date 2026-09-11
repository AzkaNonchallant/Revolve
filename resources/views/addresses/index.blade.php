@extends('layouts.app')

@section('title', 'Alamat — Revolve')

@section('content')
<div class="container-app pt-8 lg:pt-12 pb-16">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <h1 class="text-headline-lg">Alamat Saya</h1>
        <a href="{{ route('addresses.create') }}" class="btn btn-primary">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
            Tambah Alamat
        </a>
    </div>

    @include('partials.alerts')

    @if ($addresses->isNotEmpty())
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            @foreach ($addresses as $address)
                <div class="card elevation-1 bg-surface p-6">
                    <div class="flex items-center justify-between gap-3 mb-3">
                        <div class="flex items-center gap-2 flex-wrap">
                            <p class="text-title-md">{{ $address->receiver_name }}</p>
                            @if ($address->is_default)
                                <span class="badge badge-outline">Utama</span>
                            @endif
                        </div>
                    </div>
                    <p class="text-sm text-text-secondary mb-1">{{ $address->phone }}</p>
                    <p class="text-sm text-text-secondary leading-relaxed mb-5">
                        {{ $address->full_address }}, {{ $address->village ? $address->village . ', ' : '' }}{{ $address->district ? $address->district . ', ' : '' }}{{ $address->city }}, {{ $address->province }}{{ $address->postal_code ? ' ' . $address->postal_code : '' }}
                    </p>
                    <div class="flex items-center gap-2 pt-4 border-t border-surface-muted">
                        @unless ($address->is_default)
                            <form action="{{ route('addresses.default', $address) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-outline btn-sm">Jadikan Utama</button>
                            </form>
                        @endunless
                        <a href="{{ route('addresses.edit', $address) }}" class="btn btn-outline btn-sm">Ubah</a>
                        <div class="flex-grow"></div>
                        <form action="{{ route('addresses.destroy', $address) }}" method="POST" onsubmit="return confirm('Hapus alamat ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-9 h-9 flex items-center justify-center rounded-lg border border-border text-text-secondary hover:text-error hover:border-error transition-colors" aria-label="Hapus">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2m3 0v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        @if ($addresses->hasPages())
            <div class="mt-10">
                {{ $addresses->links() }}
            </div>
        @endif
    @else
        <div class="empty-state card elevation-1 bg-surface">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/>
                <circle cx="12" cy="10" r="3"/>
            </svg>
            <h3 class="text-title-md mb-2">Belum ada alamat</h3>
            <p>Tambahkan alamat untuk mempermudah proses checkout.</p>
            <a href="{{ route('addresses.create') }}" class="btn btn-primary">Tambah Alamat</a>
        </div>
    @endif
</div>
@endsection