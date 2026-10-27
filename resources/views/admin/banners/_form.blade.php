@php
    $b = $banner;
@endphp

<div>
    <label for="title" class="form-label">Judul Banner <span class="text-error">*</span></label>
    <input type="text" name="title" id="title" value="{{ old('title', $b?->title) }}" class="form-input @error('title') is-error @enderror" required>
    @error('title')
        <span class="form-error">{{ $message }}</span>
    @enderror
</div>

<div>
    <label for="image" class="form-label">Gambar Banner <span class="text-error">*</span> <span class="font-normal text-text-muted">(maks 2MB)</span></label>
    <input type="file" name="image" id="image" accept="image/*" class="form-input @error('image') is-error @enderror" {{ $b ? '' : 'required' }}>
    @error('image')
        <span class="form-error">{{ $message }}</span>
    @enderror
</div>

<div>
    <label for="link" class="form-label">Tautan <span class="font-normal text-text-muted">(opsional)</span></label>
    <input type="url" name="link" id="link" value="{{ old('link', $b?->link) }}" class="form-input" placeholder="https://...">
    @error('link')
        <span class="form-error">{{ $message }}</span>
    @enderror
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
    <div>
        <label for="type" class="form-label">Tipe <span class="text-error">*</span></label>
        <select name="type" id="type" class="form-input" required>
            <option value="hero" {{ old('type', $b?->type) == 'hero' ? 'selected' : '' }}>Hero (Halaman Utama)</option>
            <option value="promo" {{ old('type', $b?->type) == 'promo' ? 'selected' : '' }}>Promo</option>
            <option value="category" {{ old('type', $b?->type) == 'category' ? 'selected' : '' }}>Kategori</option>
        </select>
    </div>
    <div>
        <label for="category_id" class="form-label">Kategori <span class="font-normal text-text-muted">(untuk tipe kategori)</span></label>
        <select name="category_id" id="category_id" class="form-input">
            <option value="">— Pilih Kategori —</option>
            @foreach ($categories as $parent)
                <optgroup label="{{ $parent->name }}">
                    @foreach ($parent->children as $child)
                        <option value="{{ $child->id }}" {{ old('category_id', $b?->category_id) == $child->id ? 'selected' : '' }}>{{ $child->name }}</option>
                    @endforeach
                </optgroup>
            @endforeach
        </select>
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
    <div>
        <label for="sort_order" class="form-label">Urutan Tampil</label>
        <input type="number" name="sort_order" id="sort_order" min="0" step="1" value="{{ old('sort_order', $b?->sort_order ?? 0) }}" class="form-input">
        @error('sort_order')
            <span class="form-error">{{ $message }}</span>
        @enderror
    </div>
    <div class="flex items-end">
        <label class="flex items-center gap-3 cursor-pointer mb-1">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" class="w-5 h-5 accent-primary" {{ old('is_active', $b?->is_active ?? true) ? 'checked' : '' }}>
            <span class="text-sm">Aktifkan banner</span>
        </label>
    </div>
</div>