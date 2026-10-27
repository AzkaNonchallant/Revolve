@extends('layouts.dashboard')

@section('title', 'Permintaan Perubahan Data — Revolve')
@section('header', 'Permintaan Perubahan Data')

@section('content')
<div class="card elevation-1 bg-surface p-6 lg:p-8">
    <form method="GET" action="{{ route('admin.change-requests.index') }}" class="flex flex-col sm:flex-row gap-3 mb-6">
        <select name="status" class="form-input sm:w-56">
            <option value="">Semua Status</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu Review</option>
            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
        </select>
        <button type="submit" class="btn btn-outline">Filter</button>
    </form>

    @include('partials.alerts')

    @if ($requests->isNotEmpty())
        <div class="space-y-4">
            @foreach ($requests as $request)
                <a href="{{ route('admin.change-requests.show', $request) }}" class="block p-4 rounded-xl border border-border hover:border-primary transition-colors">
                    <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                        <div class="flex items-center gap-3 flex-grow min-w-0">
                            <span class="w-11 h-11 rounded-full bg-primary/10 text-primary text-sm font-bold flex items-center justify-center shrink-0">{{ strtoupper(substr($request->user?->name ?? '?', 0, 1)) }}</span>
                            <div class="min-w-0">
                                <p class="font-semibold text-sm truncate">
                                    Perubahan <span class="badge badge-outline mx-1">{{ $request->field_label }}</span>
                                </p>
                                <p class="text-xs text-text-muted truncate">{{ $request->user?->name }} · {{ $request->created_at->translatedFormat('d M Y H:i') }}</p>
                            </div>
                        </div>
                        <span class="badge {{ $request->status === 'pending' ? 'badge-warning' : ($request->status === 'approved' ? 'badge-success' : 'badge-error') }} shrink-0 self-start sm:self-auto">{{ \App\Models\UserChangeRequest::statusLabel($request->status) }}</span>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $requests->links() }}
        </div>
    @else
        <div class="empty-state"><p>Belum ada permintaan perubahan data.</p></div>
    @endif
</div>
@endsection