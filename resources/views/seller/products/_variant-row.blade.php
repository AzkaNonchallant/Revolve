@php
    $i = $row;
@endphp
<div class="variant-row grid grid-cols-2 sm:grid-cols-5 gap-3 items-center">
    <div>
        <label class="form-label text-xs">Ukuran</label>
        <input type="text" name="sizes[{{ $i }}]" class="form-input" placeholder="M" value="{{ $oldSizes[$i] ?? '' }}">
    </div>
    <div>
        <label class="form-label text-xs">Warna</label>
        <input type="text" name="colors[{{ $i }}]" class="form-input" placeholder="Hitam" value="{{ $oldColors[$i] ?? '' }}">
    </div>
    <div>
        <label class="form-label text-xs">SKU</label>
        <input type="text" name="skus[{{ $i }}]" class="form-input" value="{{ $oldSkus[$i] ?? '' }}">
    </div>
    <div>
        <label class="form-label text-xs">Harga</label>
        <input type="number" name="variant_prices[{{ $i }}]" class="form-input" value="{{ $oldPrices[$i] ?? '' }}">
    </div>
    <div class="col-span-2 sm:col-span-1 flex items-end gap-2">
        <div class="flex-grow">
            <label class="form-label text-xs">Stok</label>
            <input type="number" name="variant_stocks[{{ $i }}]" class="form-input" value="{{ $oldStocks[$i] ?? 0 }}">
        </div>
        <button type="button" onclick="removeVariantRow(this)" class="w-9 h-9 flex items-center justify-center rounded-lg border border-border text-text-secondary hover:text-error hover:border-error transition-colors">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2m3 0v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/></svg>
        </button>
    </div>
</div>