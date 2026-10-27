<div class="space-y-5">
    <div>
        <label for="shop_name" class="form-label">Nama Toko <span class="text-error">*</span></label>
        <input type="text" name="shop_name" id="shop_name" value="{{ old('shop_name') }}"
            class="form-input @error('shop_name') is-error @enderror" placeholder="cth: Toko Raka Store">
        @error('shop_name')
            <span class="form-error">{{ $message }}</span>
        @enderror
    </div>

    <div>
        <label for="shop_description" class="form-label">Deskripsi Toko</label>
        <textarea name="shop_description" id="shop_description" rows="3"
            class="form-input @error('shop_description') is-error @enderror"
            placeholder="Ceritakan produk yang akan Anda jual...">{{ old('shop_description') }}</textarea>
        @error('shop_description')
            <span class="form-error">{{ $message }}</span>
        @enderror
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div>
            <label for="phone" class="form-label">Nomor Telepon <span class="text-error">*</span></label>
            <input type="text" name="phone" id="phone" value="{{ old('phone', auth()->user()->phone_number) }}"
                class="form-input @error('phone') is-error @enderror" placeholder="08xxxxxxxxxx">
            @error('phone')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>
        <div>
            <label for="category" class="form-label">Kategori Utama Toko</label>
            <select name="category" id="category" class="form-input @error('category') is-error @enderror">
                <option value="">— Pilih Kategori —</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->name }}" {{ old('category') === $cat->name ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
            @error('category')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <div>
        <label for="address" class="form-label">Alamat Toko <span class="text-error">*</span></label>
        <textarea name="address" id="address" rows="3"
            class="form-input @error('address') is-error @enderror"
            placeholder="Alamat lengkap toko Anda...">{{ old('address') }}</textarea>
        @error('address')
            <span class="form-error">{{ $message }}</span>
        @enderror
    </div>

    <div>
        <label for="reason" class="form-label">Alasan Ingin Menjadi Seller <span class="text-error">*</span></label>
        <textarea name="reason" id="reason" rows="3"
            class="form-input @error('reason') is-error @enderror"
            placeholder="Ceritakan pengalaman dan produk Anda...">{{ old('reason') }}</textarea>
        @error('reason')
            <span class="form-error">{{ $message }}</span>
        @enderror
    </div>

    <div>
        <label for="logo" class="form-label">Logo Toko <span class="font-normal text-text-muted">(opsional, maks 2MB)</span></label>
        <input type="file" name="logo" id="logo" accept="image/*"
            class="form-input @error('logo') is-error @enderror">
        @error('logo')
            <span class="form-error">{{ $message }}</span>
        @enderror
    </div>

    <div class="flex items-center gap-3 pt-2">
        <button type="submit" class="btn btn-primary flex-grow">Kirim Pengajuan</button>
        <a href="{{ route('home') }}" class="btn btn-outline">Batal</a>
    </div>
</div>