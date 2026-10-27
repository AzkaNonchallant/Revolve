@props(['address' => null])

    @if ($errors->any())
        <div class="alert alert-error" role="alert">
            <svg class="w-5 h-5 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/>
                <path d="m15 9-6 6M9 9l6 6"/>
            </svg>
            <div>
                <p class="font-semibold mb-1">Periksa kembali data yang diisi:</p>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach ($errors->all() as $message)
                        <li>{{ $message }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="form-group mb-0! sm:col-span-2">
            <label for="receiver_name" class="form-label">Nama Penerima</label>
            <input type="text" name="receiver_name" id="receiver_name" value="{{ old('receiver_name', $address?->receiver_name) }}" required
                class="form-input @error('receiver_name') is-error @enderror" placeholder="Nama penerima">
            @error('receiver_name')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group mb-0!">
            <label for="phone" class="form-label">No. Telepon</label>
            <input type="text" name="phone" id="phone" value="{{ old('phone', $address?->phone) }}" required
                class="form-input @error('phone') is-error @enderror" placeholder="08xxxxxxxxxx">
            @error('phone')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group mb-0!">
            <label for="postal_code" class="form-label">Kode Pos <span class="font-normal text-text-muted">(opsional)</span></label>
            <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code', $address?->postal_code) }}"
                class="form-input @error('postal_code') is-error @enderror" placeholder="e.g. 40111">
            @error('postal_code')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group mb-0!">
            <label for="province" class="form-label">Provinsi</label>
            <input type="text" name="province" id="province" value="{{ old('province', $address?->province) }}" required
                class="form-input @error('province') is-error @enderror" placeholder="e.g. Jawa Barat">
            @error('province')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group mb-0!">
            <label for="city" class="form-label">Kota / Kabupaten</label>
            <input type="text" name="city" id="city" value="{{ old('city', $address?->city) }}" required
                class="form-input @error('city') is-error @enderror" placeholder="e.g. Bandung">
            @error('city')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group mb-0!">
            <label for="district" class="form-label">Kecamatan <span class="font-normal text-text-muted">(opsional)</span></label>
            <input type="text" name="district" id="district" value="{{ old('district', $address?->district) }}"
                class="form-input @error('district') is-error @enderror" placeholder="e.g. Coblong">
            @error('district')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group mb-0!">
            <label for="village" class="form-label">Kelurahan <span class="font-normal text-text-muted">(opsional)</span></label>
            <input type="text" name="village" id="village" value="{{ old('village', $address?->village) }}"
                class="form-input @error('village') is-error @enderror" placeholder="e.g. Dago">
            @error('village')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group mb-0! sm:col-span-2">
            <label for="full_address" class="form-label">Alamat Lengkap</label>
            <textarea name="full_address" id="full_address" required
                class="form-input @error('full_address') is-error @enderror" placeholder="Nama jalan, nomor rumah, patokan...">{{ old('full_address', $address?->full_address) }}</textarea>
            @error('full_address')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
        <div class="form-group mb-0!">
            <label for="latitude" class="form-label">Latitude <span class="font-normal text-text-muted">(pilih titik di peta)</span></label>
            <input type="text" name="latitude" id="latitude" value="{{ old('latitude', $address?->latitude) }}" readonly
                class="form-input @error('latitude') is-error @enderror" placeholder="Pilih titik di peta">
            @error('latitude')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group mb-0!">
            <label for="longitude" class="form-label">Longitude <span class="font-normal text-text-muted">(pilih titik di peta)</span></label>
            <input type="text" name="longitude" id="longitude" value="{{ old('longitude', $address?->longitude) }}" readonly
                class="form-input @error('longitude') is-error @enderror" placeholder="Pilih titik di peta">
            @error('longitude')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>
    </div>

    @php $mapsKey = config('services.google_maps.key'); @endphp
    @if ($mapsKey)
        <div class="mt-4">
            <div class="map-container">
                <div id="address-map" class="w-full h-64"></div>
            </div>
            <p class="text-sm text-text-muted mt-2">Geser dan klik peta untuk menandai titik lokasi alamat (opsional).</p>
        </div>
    @else
        <div class="mt-4 p-4 rounded-xl bg-surface-subtle text-sm text-text-muted">
            Klik peta untuk memilih titik koordinat. (Google Maps key belum dikonfigurasi.)
        </div>
    @endif

    <div class="mt-4">
        <label for="courier_note" class="form-label">Catatan untuk Kurir <span class="font-normal text-text-muted">(opsional)</span></label>
        <input type="text" name="courier_note" id="courier_note" value="{{ old('courier_note', $address?->courier_note) }}"
            class="form-input @error('courier_note') is-error @enderror"
            placeholder="cth: Depan gerbang merah, belok kiri setelah Alfa">
        @error('courier_note')
            <span class="form-error">{{ $message }}</span>
        @enderror
    </div>

    <label class="flex items-center gap-3 mt-6 cursor-pointer">
        <input type="checkbox" name="is_default" value="1" class="form-checkbox"
            @checked((bool) old('is_default', $address?->is_default ?? false))>
        <span class="text-sm text-text-secondary">Jadikan alamat utama</span>
    </label>

@if ($mapsKey)
@push('scripts')
<script>
(function () {
    window.initRevolveMap = function () {
        const latInput = document.getElementById('latitude');
        const lngInput = document.getElementById('longitude');
        const mapEl = document.getElementById('address-map');
        if (!latInput || !lngInput || !mapEl) return;

        const initial = {
            lat: parseFloat(latInput.value) || -6.9175,
            lng: parseFloat(lngInput.value) || 107.6191
        };

        const map = new google.maps.Map(mapEl, {
            center: initial,
            zoom: latInput.value ? 15 : 12,
            mapTypeControl: false,
            fullscreenControl: false
        });

        let marker = latInput.value && lngInput.value ? new google.maps.Marker({ map, position: initial, draggable: true }) : null;

        const update = (position) => {
            latInput.value = position.lat().toFixed(6);
            lngInput.value = position.lng().toFixed(6);
        };

        map.addListener('click', (e) => {
            if (!marker) marker = new google.maps.Marker({ map, position: e.latLng, draggable: true });
            else marker.setPosition(e.latLng);
            update(e.latLng);
        });

        if (marker) {
            marker.addListener('dragend', () => update(marker.getPosition()));
            update(marker.getPosition());
        }
    };

    if (typeof google !== 'undefined') {
        window.initRevolveMap();
    } else if (!document.getElementById('google-maps-loader')) {
        const s = document.createElement('script');
        s.id = 'google-maps-loader';
        s.async = true;
        s.src = 'https://maps.googleapis.com/maps/api/js?key={{ $mapsKey }}&callback=initRevolveMap';
        document.head.appendChild(s);
    }
})();
</script>
@endpush
@endif