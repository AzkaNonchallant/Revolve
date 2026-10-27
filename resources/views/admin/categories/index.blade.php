@extends('layouts.dashboard')

@section('title', 'Kategori — Revolve')
@section('header', 'Kelola Kategori')

@section('content')
<div class="flex items-center justify-between mb-6">
    <p class="text-sm text-text-muted">Total {{ $categories->count() }} kategori utama.</p>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
        Tambah Kategori
    </a>
</div>

@include('partials.alerts')

@if ($categories->isNotEmpty())
    <div class="space-y-6">
        @foreach ($categories as $category)
            <div class="card elevation-1 bg-surface p-6">
                <div class="flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        @if ($category->image)
                            <img src="{{ asset('storage/' . $category->image) }}" alt="" class="w-12 h-12 rounded-xl object-cover">
                        @else
                            <span class="w-12 h-12 rounded-xl bg-primary/10 text-primary font-bold flex items-center justify-center">{{ strtoupper(substr($category->name, 0, 1)) }}</span>
                        @endif
                        <div>
                            <p class="font-semibold">{{ $category->name }}</p>
                            <p class="text-xs text-text-muted">{{ $category->products_count }} produk · slug: {{ $category->slug }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <span class="badge {{ $category->is_active ? 'badge-success' : 'badge-error' }}">{{ $category->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                        <form action="{{ route('admin.categories.toggle', $category) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline btn-sm">{{ $category->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</button>
                        </form>
                        <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-outline btn-sm">Ubah</a>
                        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Hapus kategori ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline btn-sm text-error">Hapus</button>
                        </form>
                    </div>
                </div>

                @if ($category->children->isNotEmpty())
                    <div class="mt-4 pt-4 border-t border-border grid grid-cols-1 sm:grid-cols-2 gap-2">
                        @foreach ($category->children as $child)
                            <div class="flex items-center justify-between gap-3 p-3 rounded-xl bg-surface-subtle">
                                <div class="flex items-center gap-2 min-w-0">
                                    @if ($child->image)
                                        <img src="{{ asset('storage/' . $child->image) }}" alt="" class="w-8 h-8 rounded-lg object-cover">
                                    @endif
                                    <span class="text-sm font-medium truncate">{{ $child->name }}</span>
                                    <span class="text-xs text-text-muted shrink-0">· {{ $child->products_count }}</span>
                                </div>
                                <div class="flex items-center gap-1 shrink-0">
                                    <span class="badge {{ $child->is_active ? 'badge-success' : 'badge-error' }}">{{ $child->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                                    <form action="{{ route('admin.categories.toggle', $child) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-outline btn-sm">Toggle</button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endforeach
    </div>
@else
    <div class="empty-state card elevation-1 bg-surface">
        <p>Belum ada kategori.</p>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary mt-4">Buat Kategori Pertama</a>
    </div>
@endif
@endsection