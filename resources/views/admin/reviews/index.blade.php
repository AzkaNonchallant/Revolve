@extends('layouts.dashboard')

@section('title', 'Review — Revolve')
@section('header', 'Kelola Review')

@section('content')
<div class="card elevation-1 bg-surface p-6 lg:p-8">
    <form method="GET" action="{{ route('admin.reviews.index') }}" class="flex flex-col sm:flex-row gap-3 mb-6">
        <select name="hidden" class="form-input sm:w-48">
            <option value="">Semua</option>
            <option value="0" {{ request('hidden') === '0' ? 'selected' : '' }}>Tampil</option>
            <option value="1" {{ request('hidden') === '1' ? 'selected' : '' }}>Disembunyikan</option>
        </select>
        <select name="rating" class="form-input sm:w-44">
            <option value="">Semua Rating</option>
            @for ($r = 5; $r >= 1; $r--)
                <option value="{{ $r }}" {{ request('rating') == $r ? 'selected' : '' }}>{{ $r }}★</option>
            @endfor
        </select>
        <button type="submit" class="btn btn-outline">Filter</button>
    </form>

    @include('partials.alerts')

    @if ($reviews->isNotEmpty())
        <div class="space-y-4">
            @foreach ($reviews as $review)
                <div class="p-4 rounded-xl border border-border">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-11 h-11 rounded-full bg-primary/10 text-primary font-bold flex items-center justify-center shrink-0">{{ strtoupper(substr($review->user?->name ?? '?', 0, 1)) }}</div>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold">{{ $review->user?->name }}</p>
                                <div class="stars" data-rating="{{ $review->rating }}">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <svg viewBox="0 0 20 20" fill="{{ $i <= $review->rating ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"><path d="M10 1.5 12.4 6l5 .7-3.7 3.6.9 5.2L10 13.4l-4.6 2.4.9-5.2L2.6 6.7 7.6 6z"/></svg>
                                    @endfor
                                </div>
                            </div>
                        </div>
                        <span class="badge {{ $review->is_hidden ? 'badge-warning' : 'badge-success' }} self-start sm:self-auto">{{ $review->is_hidden ? 'Disembunyikan' : 'Tampil' }}</span>
                    </div>
                    <p class="text-sm text-text-secondary mt-3">{{ $review->review ?? 'Tidak ada komentar.' }}</p>
                    @if ($review->image)
                        <img src="{{ asset('storage/' . $review->image) }}" alt="" class="mt-3 w-24 h-24 rounded-xl object-cover">
                    @endif
                    @if ($review->product)
                        <a href="{{ route('admin.products.show', $review->product) }}" class="text-xs text-primary font-semibold mt-3 inline-block">Produk: {{ $review->product->name }} →</a>
                    @endif
                    <div class="flex items-center justify-between gap-3 mt-3 pt-3 border-t border-border">
                        <p class="text-xs text-text-muted">{{ $review->created_at->translatedFormat('d M Y H:i') }}</p>
                        <div class="flex items-center gap-2">
                            <form action="{{ route('admin.reviews.toggle', $review) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-outline btn-sm">{{ $review->is_hidden ? 'Tampilkan' : 'Sembunyikan' }}</button>
                            </form>
                            <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" onsubmit="return confirm('Hapus review ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline btn-sm text-error">Hapus</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $reviews->links() }}
        </div>
    @else
        <div class="empty-state"><p>Review tidak ditemukan.</p></div>
    @endif
</div>
@endsection