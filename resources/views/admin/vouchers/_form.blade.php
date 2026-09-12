@php
    $v = $voucher;
@endphp

<div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
    <div>
        <label for="code" class="form-label">Kode Voucher <span class="text-error">*</span></label>
        <input type="text" name="code" id="code" value="{{ old('code', $v?->code) }}" class="form-input uppercase @error('code') is-error @enderror" placeholder="cth: DISKON10" required>
        @error('code')
            <span class="form-error">{{ $message }}</span>
        @enderror
    </div>
    <div>
        <label for="name" class="form-label">Nama Voucher <span class="text-error">*</span></label>
        <input type="text" name="name" id="name" value="{{ old('name', $v?->name) }}" class="form-input @error('name') is-error @enderror" placeholder="cth: Diskon Besar Akhir Tahun" required>
        @error('name')
            <span class="form-error">{{ $message }}</span>
        @enderror
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
    <div>
        <label for="type" class="form-label">Jenis <span class="text-error">*</span></label>
        <select name="type" id="type" class="form-input" required>
            <option value="percent" {{ old('type', $v?->type) == 'percent' ? 'selected' : '' }}>Persen (%)</option>
            <option value="nominal" {{ old('type', $v?->type) == 'nominal' ? 'selected' : '' }}>Nominal (Rp)</option>
        </select>
    </div>
    <div>
        <label for="value" class="form-label">Nilai <span class="text-error">*</span></label>
        <input type="number" name="value" id="value" min="0" step="1" value="{{ old('value', $v?->value) }}" class="form-input" required>
        @error('value')
            <span class="form-error">{{ $message }}</span>
        @enderror
    </div>
    <div>
        <label for="min_purchase" class="form-label">Min. Belanja (Rp)</label>
        <input type="number" name="min_purchase" id="min_purchase" min="0" step="1" value="{{ old('min_purchase', $v?->min_purchase) }}" class="form-input">
        @error('min_purchase')
            <span class="form-error">{{ $message }}</span>
        @enderror
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
    <div>
        <label for="max_discount" class="form-label">Maks. Diskon (Rp)</label>
        <input type="number" name="max_discount" id="max_discount" min="0" step="1" value="{{ old('max_discount', $v?->max_discount) }}" class="form-input">
        @error('max_discount')
            <span class="form-error">{{ $message }}</span>
        @enderror
    </div>
    <div>
        <label for="quota" class="form-label">Kuota Pemakaian</label>
        <input type="number" name="quota" id="quota" min="0" step="1" value="{{ old('quota', $v?->quota) }}" class="form-input" placeholder="Kosong = tanpa batas">
        @error('quota')
            <span class="form-error">{{ $message }}</span>
        @enderror
    </div>
    <div>
        <label for="scope" class="form-label">Cakupan <span class="text-error">*</span></label>
        <select name="scope" id="scope" class="form-input" required>
            <option value="marketplace" {{ old('scope', $v?->scope) == 'marketplace' ? 'selected' : '' }}>Marketplace</option>
            <option value="category" {{ old('scope', $v?->scope) == 'category' ? 'selected' : '' }}>Kategori</option>
            <option value="seller" {{ old('scope', $v?->scope) == 'seller' ? 'selected' : '' }}>Seller</option>
        </select>
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
    <div id="category-select" @if (old('scope', $v?->scope) !== 'category') hidden @endif>
        <label for="category_id" class="form-label">Kategori</label>
        <select name="category_id" id="category_id" class="form-input">
            <option value="">— Pilih Kategori —</option>
            @foreach ($categories as $parent)
                <optgroup label="{{ $parent->name }}">
                    @foreach ($parent->children as $child)
                        <option value="{{ $child->id }}" {{ old('category_id', $v?->category_id) == $child->id ? 'selected' : '' }}>{{ $child->name }}</option>
                    @endforeach
                </optgroup>
            @endforeach
        </select>
        @error('category_id')
            <span class="form-error">{{ $message }}</span>
        @enderror
    </div>
    <div id="seller-select" @if (old('scope', $v?->scope) !== 'seller') hidden @endif>
        <label for="seller_id" class="form-label">Seller</label>
        <select name="seller_id" id="seller_id" class="form-input">
            <option value="">— Pilih Seller —</option>
            @foreach ($sellers as $seller)
                <option value="{{ $seller->id }}" {{ old('seller_id', $v?->seller_id) == $seller->id ? 'selected' : '' }}>{{ $seller->shop?->name ?? $seller->name }}</option>
            @endforeach
        </select>
        @error('seller_id')
            <span class="form-error">{{ $message }}</span>
        @enderror
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
    <div>
        <label for="starts_at" class="form-label">Mulai Berlaku</label>
        <input type="date" name="starts_at" id="starts_at" value="{{ old('starts_at', $v?->starts_at?->format('Y-m-d')) }}" class="form-input">
        @error('starts_at')
            <span class="form-error">{{ $message }}</span>
        @enderror
    </div>
    <div>
        <label for="ends_at" class="form-label">Berakhir</label>
        <input type="date" name="ends_at" id="ends_at" value="{{ old('ends_at', $v?->ends_at?->format('Y-m-d')) }}" class="form-input">
        @error('ends_at')
            <span class="form-error">{{ $message }}</span>
        @enderror
    </div>
</div>

<label class="flex items-center gap-3 cursor-pointer">
    <input type="hidden" name="is_active" value="0">
    <input type="checkbox" name="is_active" value="1" class="w-5 h-5 accent-primary" {{ old('is_active', $v?->is_active ?? true) ? 'checked' : '' }}>
    <span class="text-sm">Aktifkan voucher</span>
</label>

@push('scripts')
<script>
    const scopeEl = document.getElementById('scope');
    const categoryEl = document.getElementById('category-select');
    const sellerEl = document.getElementById('seller-select');

    function syncScope() {
        const isCategory = scopeEl.value === 'category';
        const isSeller = scopeEl.value === 'seller';
        categoryEl.hidden = !isCategory;
        sellerEl.hidden = !isSeller;
    }

    scopeEl.addEventListener('change', syncScope);
    syncScope();
</script>
@endpush