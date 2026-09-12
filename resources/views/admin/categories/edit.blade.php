@extends('layouts.dashboard')

@section('title', 'Ubah Kategori — Revolve')
@section('header', 'Ubah Kategori: {{ $category->name }}')

@section('content')
@include('partials.alerts')

<div class="card elevation-1 bg-surface p-6 lg:p-8 max-w-2xl">
    <form action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf
        @method('PUT')

        @if ($category->image)
            <div class="flex items-center gap-4">
                <img src="{{ asset('storage/' . $category->image) }}" alt="" class="w-24 h-24 rounded-2xl object-cover">
                <p class="text-xs text-text-muted">Gambar saat ini. Upload gambar baru untuk menggantinya.</p>
            </div>
        @endif

        <div>
            <label for="name" class="form-label">Nama Kategori <span class="text-error">*</span></label>
            <input type="text" name="name" id="name" value="{{ old('name', $category->name) }}" class="form-input @error('name') is-error @enderror" required>
            @error('name')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label for="parent_id" class="form-label">Kategori Induk <span class="font-normal text-text-muted">(opsional)</span></label>
            <select name="parent_id" id="parent_id" class="form-input">
                <option value="">— Kategori Utama —</option>
                @foreach ($parents as $parent)
                    <option value="{{ $parent->id }}" {{ old('parent_id', $category->parent_id) == $parent->id ? 'selected' : '' }}>{{ $parent->name }}</option>
                @endforeach
            </select>
            @error('parent_id')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label for="image" class="form-label">Gambar Kategori <span class="font-normal text-text-muted">(opsional, maks 2MB)</span></label>
            <input type="file" name="image" id="image" accept="image/*" class="form-input">
            @error('image')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <label class="flex items-center gap-3 cursor-pointer">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" class="w-5 h-5 accent-primary" {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
            <span class="text-sm">Aktifkan kategori</span>
        </label>

        <div class="flex flex-col sm:flex-row gap-3 mt-6 pt-6 border-t border-border">
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            <a href="{{ route('admin.categories.index') }}" class="btn btn-outline">Batal</a>
        </div>
    </form>
</div>
@endsection