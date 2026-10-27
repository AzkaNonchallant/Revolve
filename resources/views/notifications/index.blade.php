@extends('layouts.app')

@section('title', 'Notifikasi — Revolve')

@section('content')
<div class="container-app pt-8 lg:pt-12 pb-16 max-w-3xl">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <h1 class="text-headline-lg">Notifikasi</h1>
        @if (auth()->user()->unreadNotifications->isNotEmpty())
            <form action="{{ route('notifications.read-all') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline btn-sm">Tandai Semua Dibaca</button>
            </form>
        @endif
    </div>

    @include('partials.alerts')

    @if ($notifications->isNotEmpty())
        <div class="space-y-2">
            @foreach ($notifications as $notification)
                @php
                    $data = (array) $notification->data;
                    $isRead = $loop->index < (int) auth()->user()->readNotifications->count();
                @endphp
                <a href="{{ $data['url'] ?? '#' }}" class="notif-item card elevation-1 bg-surface {{ $notification->read_at ? 'is-read' : 'border-primary/30' }} hover:border-primary/60 transition-colors">
                    <span class="notif-dot"></span>
                    <span class="flex-grow">
                        <span class="flex items-center justify-between gap-2">
                            <span class="text-sm font-semibold {{ $notification->read_at ? 'text-text-secondary' : 'text-text-primary' }}">{{ $data['title'] ?? 'Notifikasi' }}</span>
                            <span class="text-xs text-text-muted shrink-0">{{ $notification->created_at->diffForHumans() }}</span>
                        </span>
                        <span class="block text-sm text-text-secondary mt-0.5">{{ $data['message'] ?? '' }}</span>
                        @if ($notification->read_at)
                            <span class="text-xs text-text-muted">Dibaca {{ $notification->read_at->diffForHumans() }}</span>
                        @endif
                    </span>
                </a>
            @endforeach
        </div>

        @if ($notifications->hasPages())
            <div class="mt-8">
                {{ $notifications->links() }}
            </div>
        @endif
    @else
        <div class="empty-state card elevation-1 bg-surface">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
            <h3 class="text-title-md mb-2">Belum ada notifikasi</h3>
            <p>Anda akan menerima notifikasi untuk aktivitas pesanan dan marketplace.</p>
        </div>
    @endif
</div>
@endsection