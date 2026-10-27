@extends('layouts.dashboard')

@section('title', 'Notifikasi — Revolve')
@section('header', 'Notifikasi')

@section('content')
@include('partials.alerts')

<div class="card elevation-1 bg-surface p-6 lg:p-8">
    @if ($notifications->whereNull('read_at')->count() > 0)
        <form action="{{ route('admin.notifications.read-all') }}" method="POST" class="mb-6">
            @csrf
            <button type="submit" class="btn btn-outline btn-sm">Tandai Semua Dibaca</button>
        </form>
    @endif

    @if ($notifications->isNotEmpty())
        <div class="space-y-2">
            @foreach ($notifications as $notification)
                <a href="{{ $notification->data['url'] ?? '#' }}" class="block p-4 rounded-xl border border-border hover:border-primary transition-colors {{ $notification->read_at ? '' : 'bg-primary/5' }}">
                    <div class="flex items-center justify-between gap-3">
                        <p class="text-sm font-semibold">{{ $notification->data['title'] ?? 'Notifikasi' }}</p>
                        @unless ($notification->read_at)
                            <span class="badge badge-primary shrink-0">Baru</span>
                        @endunless
                    </div>
                    <p class="text-sm text-text-secondary mt-1">{{ $notification->data['message'] ?? '' }}</p>
                    @if ($notification->data['order_number'] ?? null)
                        <p class="text-xs text-text-muted mt-1">Pesanan: {{ $notification->data['order_number'] }}</p>
                    @endif
                    <p class="text-xs text-text-muted mt-2">{{ $notification->created_at->diffForHumans() }}</p>
                </a>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $notifications->links() }}
        </div>
    @else
        <div class="empty-state"><p>Belum ada notifikasi.</p></div>
    @endif
</div>
@endsection