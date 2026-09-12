@extends('layouts.dashboard')

@section('title', 'Detail Permintaan Perubahan — Revolve')
@section('header', 'Permintaan Perubahan Data')

@section('content')
@include('partials.alerts')

<div class="max-w-3xl">
    <div class="card elevation-1 bg-surface p-6 lg:p-8 mb-6">
        <div class="flex items-center justify-between gap-4 mb-6">
            <div>
                <h2 class="text-headline-sm">Perubahan {{ $changeRequest->field_label }}</h2>
                <p class="text-sm text-text-muted mt-1">Diajukan {{ $changeRequest->created_at->translatedFormat('d M Y H:i') }}</p>
            </div>
            <span class="badge {{ $changeRequest->status === 'pending' ? 'badge-warning' : ($changeRequest->status === 'approved' ? 'badge-success' : 'badge-error') }}">{{ \App\Models\UserChangeRequest::statusLabel($changeRequest->status) }}</span>
        </div>

        <dl class="space-y-5 text-sm">
            <div class="p-4 rounded-xl bg-surface-subtle flex items-center justify-between gap-4">
                <div>
                    <dt class="text-text-muted text-xs">Pemohon</dt>
                    <dd class="font-semibold">{{ $changeRequest->user?->name }}</dd>
                </div>
                <div class="text-right">
                    <dt class="text-text-muted text-xs">Email</dt>
                    <dd class="font-semibold">{{ $changeRequest->user?->email }}</dd>
                </div>
            </div>
            <div>
                <dt class="text-text-muted text-xs mb-1">Data Saat Ini</dt>
                <dd class="text-sm text-text-secondary bg-surface-subtle rounded-lg px-4 py-3">{{ $changeRequest->old_value ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-text-muted text-xs mb-1">Permintaan Baru</dt>
                <dd class="text-sm font-semibold bg-success/5 border border-success/30 rounded-lg px-4 py-3">{{ $changeRequest->new_value }}</dd>
            </div>
            @if ($changeRequest->reason)
                <div>
                    <dt class="text-text-muted text-xs mb-1">Alasan</dt>
                    <dd class="text-sm">{{ $changeRequest->reason }}</dd>
                </div>
            @endif
            @if ($changeRequest->admin_note)
                <div>
                    <dt class="text-text-muted text-xs mb-1">Catatan Admin</dt>
                    <dd class="text-sm">{{ $changeRequest->admin_note }}</dd>
                </div>
            @endif
        </dl>
    </div>

    @if ($changeRequest->status === 'pending')
        <div class="card elevation-1 bg-surface p-6">
            <div class="flex flex-col sm:flex-row gap-3">
                <form action="{{ route('admin.change-requests.approve', $changeRequest) }}" method="POST" class="flex-grow">
                    @csrf
                    <button type="submit" class="btn btn-primary w-full">Setujui Perubahan</button>
                </form>
                <form action="{{ route('admin.change-requests.reject', $changeRequest) }}" method="POST">
                    @csrf
                    <div class="flex flex-col sm:flex-row gap-2">
                        <input type="text" name="reason" placeholder="Alasan penolakan..." class="form-input" required>
                        <button type="submit" class="btn btn-outline text-error shrink-0" onclick="return confirm('Tolak permintaan ini?')">Tolak</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
@endsection