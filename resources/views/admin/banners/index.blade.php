@extends('layouts.dashboard')

@section('title', 'Banner — Revolve')
@section('header', 'Kelola Banner')

@section('content')
<div class="flex items-center justify-between mb-6">
    <p class="text-sm text-text-muted">Banner tipe <code>hero</code> tampil di halaman utama.</p>
    <a href="{{ route('admin.banners.create') }}" class="btn btn-primary">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
        Tambah Banner
    </a>
</div>

@include('partials.alerts')

@if ($banners->isNotEmpty())
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @foreach ($banners as $banner)
            <div class="card elevation-1 bg-surface overflow-hidden">
                @if ($banner->image)
                    <div class="aspect-[16/9] bg-surface-muted">
                        <img src="{{ asset('storage/' . $banner->image) }}" alt="{{ $banner->title }}" class="w-full h-full object-cover">
                    </div>
                @else
                    <div class="aspect-[16/9] bg-surface-muted flex items-center justify-center text-text-muted text-sm">Tanpa gambar</div>
                @endif
                <div class="p-5">
                    <div class="flex items-center gap-2 flex-wrap mb-2">
                        @if ($banner->category)
                            <span class="badge badge-outline">{{ $banner->category->name }}</span>
                        @endif
                        <span class="badge {{ $banner->type === 'hero' ? 'badge-primary' : 'badge-muted' }}">{{ $banner->type }}</span>
                        <span class="badge {{ $banner->is_active ? 'badge-success' : 'badge-error' }}">{{ $banner->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                    </div>
                    <p class="font-semibold">{{ $banner->title }}</p>
                    @if ($banner->link)
                        <p class="text-xs text-text-muted mt-1 truncate">{{ $banner->link }}</p>
                    @endif
                    <p class="text-xs text-text-muted mt-1">Urutan: {{ $banner->sort_order ?? 0 }}</p>
                    <div class="flex items-center gap-2 mt-4 pt-4 border-t border-border">
                        <a href="{{ route('admin.banners.edit', $banner) }}" class="btn btn-outline btn-sm">Ubah</a>
                        <form action="{{ route('admin.banners.toggle', $banner) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline btn-sm">{{ $banner->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</button>
                        </form>
                        <form action="{{ route('admin.banners.destroy', $banner) }}" method="POST" onsubmit="return confirm('Hapus banner ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline btn-sm text-error">Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="empty-state card elevation-1 bg-surface">
        <p>Belum ada banner.</p>
        <a href="{{ route('admin.banners.create') }}" class="btn btn-primary mt-4">Buat Banner</a>
    </div>
@endif
@endsection