@extends('layouts.dashboard')

@section('title', 'Profil Toko — Revolve')
@section('header', 'Profil Toko')

@section('content')
@php $shop = auth()->user()->shop; @endphp
<div class="max-w-3xl">
    <div class="card elevation-1 bg-surface p-6 lg:p-8 mb-6">
        <div class="flex items-center gap-4 mb-6">
            @if ($shop?->logo)
                <img src="{{ asset('storage/' . $shop->logo) }}" alt="{{ $shop->name }}" class="w-20 h-20 rounded-2xl object-cover">
            @else
                <span class="w-20 h-20 rounded-2xl bg-obsidian text-white text-2xl font-bold flex items-center justify-center">{{ strtoupper(substr($shop->name ?? 'T', 0, 1)) }}</span>
            @endif
            <div>
                <p class="text-headline-sm font-semibold">{{ $shop?->name ?? 'Belum ada toko' }}</p>
                <p class="text-sm text-text-muted">Toko Anda selalu tampil di {{ route('shops.show', $shop) ? 'halaman publik' : 'marketplace' }}</p>
                @if ($shop?->is_verified)
                    <span class="badge badge-success mt-1">Terverifikasi</span>
                @endif
            </div>
        </div>
    </div>

    @if (! $shop)
        <div class="empty-state card elevation-1 bg-surface">
            <p>Toko belum dibuat. Hubungi Admin untuk membuat toko Anda.</p>
        </div>
    @else
        <form action="{{ route('seller.shop.update') }}" method="POST" enctype="multipart/form-data" class="card elevation-1 bg-surface p-6 lg:p-8 space-y-5">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="name" class="form-label">Nama Toko <span class="text-error">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name', $shop->name) }}" class="form-input @error('name') is-error @enderror">
                    @error('name')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="slug" class="form-label">Slug Toko <span class="text-error">*</span></label>
                    <input type="text" name="slug" id="slug" value="{{ old('slug', $shop->slug) }}" class="form-input @error('slug') is-error @enderror">
                    @error('slug')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div>
                <label for="description" class="form-label">Deskripsi Toko</label>
                <textarea name="description" id="description" rows="3" class="form-input @error('description') is-error @enderror">{{ old('description', $shop->description) }}</textarea>
                @error('description')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="phone" class="form-label">Nomor Telepon</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $shop->phone) }}" class="form-input @error('phone') is-error @enderror">
                    @error('phone')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="category_id" class="form-label">Kategori Utama</label>
                    <select name="category_id" id="category_id" class="form-input @error('category_id') is-error @enderror">
                        <option value="">— Pilih Kategori —</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $shop->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div>
                <label for="address" class="form-label">Alamat Toko</label>
                <textarea name="address" id="address" rows="2" class="form-input @error('address') is-error @enderror">{{ old('address', $shop->address) }}</textarea>
                @error('address')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="logo" class="form-label">Logo Toko <span class="font-normal text-text-muted">(maks 2MB)</span></label>
                    <input type="file" name="logo" id="logo" accept="image/*" class="form-input @error('logo') is-error @enderror">
                    @error('logo')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="banner" class="form-label">Banner Toko <span class="font-normal text-text-muted">(maks 2MB)</span></label>
                    <input type="file" name="banner" id="banner" accept="image/*" class="form-input @error('banner') is-error @enderror">
                    @error('banner')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                <a href="{{ route('shops.show', $shop) }}" class="btn btn-outline">Lihat Toko Publik</a>
            </div>
        </form>
    @endif
</div>
@endsection