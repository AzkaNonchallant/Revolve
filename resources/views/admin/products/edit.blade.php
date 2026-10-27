@extends('layouts.dashboard')

@section('title', 'Ubah Produk — Revolve')
@section('header', 'Ubah Produk')

@section('content')
@include('partials.alerts')

<div class="card elevation-1 bg-surface p-6 lg:p-8 max-w-4xl">
    <a href="{{ route('admin.products.show', $product) }}" class="inline-flex items-center gap-2 text-sm text-text-secondary hover:text-text-primary mb-6">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        Kembali ke Detail Produk
    </a>

    <form action="{{ route('admin.products.update', $product) }}" method="POST" class="space-y-5">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label for="name" class="form-label">Nama Produk <span class="text-error">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" class="form-input" required>
                @error('name')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label for="category_id" class="form-label">Kategori <span class="text-error">*</span></label>
                <select name="category_id" id="category_id" class="form-input" required>
                    @foreach ($categories as $parent)
                        <optgroup label="{{ $parent->name }}">
                            @foreach ($parent->children as $child)
                                <option value="{{ $child->id }}" {{ old('category_id', $product->category_id) == $child->id ? 'selected' : '' }}>{{ $child->name }}</option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
                @error('category_id')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div>
            <label for="brand_id" class="form-label">Brand</label>
            <select name="brand_id" id="brand_id" class="form-input">
                <option value="">— Pilih Brand —</option>
                @foreach ($brands as $brand)
                    <option value="{{ $brand->id }}" {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="description" class="form-label">Deskripsi</label>
            <textarea name="description" id="description" rows="5" class="form-input">{{ old('description', $product->description) }}</textarea>
        </div>

        <div>
            <label for="material" class="form-label">Bahan</label>
            <input type="text" name="material" id="material" value="{{ old('material', $product->material) }}" class="form-input">
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
            <div>
                <label for="price" class="form-label">Harga (Rp) <span class="text-error">*</span></label>
                <input type="number" name="price" id="price" min="0" step="1" value="{{ old('price', $product->price) }}" class="form-input" required>
                @error('price')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label for="discount_price" class="form-label">Harga Diskon</label>
                <input type="number" name="discount_price" id="discount_price" min="0" step="1" value="{{ old('discount_price', $product->discount_price) }}" class="form-input">
                @error('discount_price')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label for="stock" class="form-label">Stok <span class="text-error">*</span></label>
                <input type="number" name="stock" id="stock" min="0" step="1" value="{{ old('stock', $product->stock) }}" class="form-input" required>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label for="weight" class="form-label">Berat (gram) <span class="text-error">*</span></label>
                <input type="number" name="weight" id="weight" min="1" step="1" value="{{ old('weight', $product->weight) }}" class="form-input" required>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 mt-8 pt-6 border-t border-border">
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            <a href="{{ route('admin.products.show', $product) }}" class="btn btn-outline">Batal</a>
        </div>
    </form>
</div>
@endsection