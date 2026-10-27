@php
    $selectedProduct = $product ?? null;
    $selectedCategoryId = old('category_id', $selectedProduct?->category_id);
    $editing = $selectedProduct !== null;
@endphp

<div class="space-y-6">
    <div>
        <label for="name" class="form-label">Nama Produk <span class="text-error">*</span></label>
        <input type="text" name="name" id="name" value="{{ old('name', $selectedProduct?->name) }}" class="form-input @error('name') is-error @enderror" placeholder="cth: Sepatu Sneaker XYZ" required>
        @error('name')
            <span class="form-error">{{ $message }}</span>
        @enderror
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div>
            <label for="category_id" class="form-label">Kategori <span class="text-error">*</span></label>
            <select name="category_id" id="category_id" class="form-input @error('category_id') is-error @enderror">
                <option value="">— Pilih Kategori —</option>
                @foreach ($categories as $parent)
                    <optgroup label="{{ $parent->name }}">
                        @foreach ($parent->children as $child)
                            <option value="{{ $child->id }}" {{ (string) $selectedCategoryId === (string) $child->id ? 'selected' : '' }}>
                                {{ $child->name }}
                            </option>
                        @endforeach
                    </optgroup>
                @endforeach
            </select>
            @error('category_id')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>
        <div>
            <label for="brand_id" class="form-label">Brand</label>
            <select name="brand_id" id="brand_id" class="form-input @error('brand_id') is-error @enderror">
                <option value="">— Pilih Brand —</option>
                @foreach ($brands as $brand)
                    <option value="{{ $brand->id }}" {{ old('brand_id', $selectedProduct?->brand_id) == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                @endforeach
            </select>
            @error('brand_id')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <div>
        <label for="description" class="form-label">Deskripsi Produk</label>
        <textarea name="description" id="description" rows="5" class="form-input @error('description') is-error @enderror" placeholder="Ceritakan detail, bahan, keunggulan produk...">{{ old('description', $selectedProduct?->description) }}</textarea>
        @error('description')
            <span class="form-error">{{ $message }}</span>
        @enderror
    </div>

    <div>
        <label for="material" class="form-label">Bahan <span class="font-normal text-text-muted">(opsional)</span></label>
        <input type="text" name="material" id="material" value="{{ old('material', $selectedProduct?->material) }}" class="form-input" placeholder="cth: Katun premium">
        @error('material')
            <span class="form-error">{{ $message }}</span>
        @enderror
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div>
            <label for="price" class="form-label">Harga (Rp) <span class="text-error">*</span></label>
            <input type="number" name="price" id="price" min="0" step="1" value="{{ old('price', $selectedProduct?->price) }}" required class="form-input @error('price') is-error @enderror">
            @error('price')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>
        <div>
            <label for="discount_price" class="form-label">Harga Diskon <span class="font-normal text-text-muted">(opsional)</span></label>
            <input type="number" name="discount_price" id="discount_price" min="0" step="1" value="{{ old('discount_price', $selectedProduct?->discount_price) }}" class="form-input @error('discount_price') is-error @enderror">
            @error('discount_price')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>
        <div>
            <label for="stock" class="form-label">Stok <span class="text-error">*</span></label>
            <input type="number" name="stock" id="stock" min="0" step="1" value="{{ old('stock', $selectedProduct?->stock) }}" required class="form-input @error('stock') is-error @enderror">
            @error('stock')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div>
            <label for="weight" class="form-label">Berat (gram) <span class="text-error">*</span></label>
            <input type="number" name="weight" id="weight" min="1" step="1" value="{{ old('weight', $selectedProduct?->weight) }}" required class="form-input @error('weight') is-error @enderror">
            @error('weight')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>
        <div class="flex items-end pb-1">
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="hidden" name="status" value="0">
                <input type="checkbox" name="status" value="1" class="w-5 h-5 accent-primary" {{ old('status', $selectedProduct?->status ?? 1) ? 'checked' : '' }}>
                <span class="text-sm">Produk langsung aktif (jika disetujui)</span>
            </label>
        </div>
    </div>

    <div class="border-t border-border pt-6">
        <label for="images" class="form-label">Foto Produk <span class="font-normal text-text-muted">(maks 6, format gambar)</span></label>
        <input type="file" name="images[]" id="images" accept="image/*" multiple class="form-input @error('images') is-error @enderror">
        @error('images')
            <span class="form-error">{{ $message }}</span>
        @enderror
        @error('images.*')
            <span class="form-error">{{ $message }}</span>
        @enderror

        @if ($editing && $selectedProduct->images->isNotEmpty())
            <div class="thumb-grid mt-4">
                @foreach ($selectedProduct->images as $img)
                    <div class="relative group">
                        <img src="{{ asset('storage/' . $img->path) }}" alt="" class="w-full h-full object-cover">
                        @if ($img->is_primary)
                            <span class="absolute top-1 left-1 badge badge-primary">Utama</span>
                        @endif
                        <span class="absolute inset-0 flex items-center justify-center bg-obsidian/50 text-white text-xs font-semibold opacity-0 group-hover:opacity-100 transition-opacity clamp-4">Hapus lewat upload baru</span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="border-t border-border pt-6" id="variant-section">
        <div class="flex items-center justify-between mb-3">
            <div>
                <p class="form-label mb-0">Varian Produk <span class="font-normal text-text-muted">(ukuran / warna — opsional)</span></p>
                <p class="text-xs text-text-muted mt-1">Kosongkan jika tidak menggunakan varian (stok utama yang dipakai).</p>
            </div>
            <button type="button" class="btn btn-outline btn-sm" onclick="addVariantRow()">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
                Tambah Baris
            </button>
        </div>

        <div id="variant-rows" class="space-y-3">
            @php
                $oldSizes = old('sizes', $editing ? $selectedProduct->variants->pluck('size')->toArray() : []);
                $oldColors = old('colors', $editing ? $selectedProduct->variants->pluck('color')->toArray() : []);
                $oldSkus = old('skus', $editing ? $selectedProduct->variants->pluck('sku')->toArray() : []);
                $oldPrices = old('variant_prices', $editing ? $selectedProduct->variants->pluck('price')->toArray() : []);
                $oldStocks = old('variant_stocks', $editing ? $selectedProduct->variants->pluck('stock')->toArray() : []);
                $rows = max(count($oldSizes), count($oldColors));
            @endphp

            @for ($i = 0; $i < $rows; $i++)
                @include('seller.products._variant-row', ['row' => $i])
            @endfor
        </div>
    </div>
</div>

@push('scripts')
<script>
    let rowIndex = {{ $rows ?? 0 }};

    function variantRowTemplate(pi) {
        return `
        <div class="variant-row grid grid-cols-2 sm:grid-cols-5 gap-3 items-center">
            <div>
                <label class="form-label text-xs">Ukuran</label>
                <input type="text" name="sizes[${pi}]" class="form-input" placeholder="M" value="">
            </div>
            <div>
                <label class="form-label text-xs">Warna</label>
                <input type="text" name="colors[${pi}]" class="form-input" placeholder="Hitam" value="">
            </div>
            <div>
                <label class="form-label text-xs">SKU</label>
                <input type="text" name="skus[${pi}]" class="form-input" placeholder="" value="">
            </div>
            <div>
                <label class="form-label text-xs">Harga</label>
                <input type="number" name="variant_prices[${pi}]" class="form-input" placeholder="" value="">
            </div>
            <div class="col-span-2 sm:col-span-1 flex items-end gap-2">
                <div class="flex-grow">
                    <label class="form-label text-xs">Stok</label>
                    <input type="number" name="variant_stocks[${pi}]" class="form-input" value="0">
                </div>
                <button type="button" onclick="removeVariantRow(this)" class="w-9 h-9 flex items-center justify-center rounded-lg border border-border text-text-secondary hover:text-error hover:border-error transition-colors">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2m3 0v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/></svg>
                </button>
            </div>
        </div>`;
    }

    function addVariantRow() {
        document.getElementById('variant-rows').insertAdjacentHTML('beforeend', variantRowTemplate(rowIndex));
        rowIndex++;
    }

    function removeVariantRow(btn) {
        btn.closest('.variant-row').remove();
    }
</script>
@endpush