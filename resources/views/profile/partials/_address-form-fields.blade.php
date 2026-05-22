@php
    $isEdit = isset($address);
    $val = fn(string $field) => old($field, $isEdit ? $address->$field : ($field === 'label' ? 'Rumah' : ''));

    // For edit mode, pre-build the "ID|Name" values for province and city selects
    $preProvince = $isEdit && $address->province_id
        ? $address->province_id . '|' . $address->province
        : old('province', '');
    $preCity = $isEdit && $address->city_id
        ? $address->city_id . '|' . $address->city
        : old('city', '');
    $preProvinceId = $isEdit ? ($address->province_id ?? '') : '';
    $uniqueId = $isEdit ? 'edit-' . $address->id : 'add';
@endphp

{{-- Alpine.js component wrapper for province→city cascade --}}
<div x-data="{
    provinces: [],
    cities: [],
    loadingProvinces: false,
    loadingCities: false,
    selectedProvince: '{{ addslashes($preProvince) }}',
    selectedCity: '{{ addslashes($preCity) }}',
    preProvinceId: '{{ $preProvinceId }}',

    init() {
        this.loadProvinces();
    },

    async loadProvinces() {
        this.loadingProvinces = true;
        try {
            const res = await fetch('{{ route('api.provinces') }}');
            const json = await res.json();
            this.provinces = json.data || [];

            // If editing, trigger city load after provinces are loaded
            if (this.preProvinceId) {
                await this.loadCities(this.preProvinceId);
            }
        } catch (e) {
            console.error('Failed to load provinces', e);
        }
        this.loadingProvinces = false;
    },

    async onProvinceChange(val) {
        this.selectedProvince = val;
        this.selectedCity = '';
        this.cities = [];
        if (!val) return;
        const provinceId = val.split('|')[0];
        await this.loadCities(provinceId);
    },

    async loadCities(provinceId) {
        this.loadingCities = true;
        try {
            const res = await fetch('/api/cities/' + provinceId);
            const json = await res.json();
            this.cities = json.data || [];
        } catch (e) {
            console.error('Failed to load cities', e);
        }
        this.loadingCities = false;
    }
}" class="space-y-4">

    {{-- ── Label ── --}}
    <div>
        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Label Alamat</label>
        <div class="flex gap-2 flex-wrap">
            @foreach(['Rumah', 'Kantor', 'Kost', 'Lainnya'] as $labelOption)
            <label class="cursor-pointer">
                <input type="radio" name="label" value="{{ $labelOption }}"
                       {{ $val('label') === $labelOption ? 'checked' : '' }}
                       class="peer sr-only">
                <span class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-1.5 text-xs font-semibold text-slate-600 dark:text-slate-400 cursor-pointer peer-checked:border-indigo-500 peer-checked:bg-indigo-50 peer-checked:text-indigo-700 dark:peer-checked:bg-indigo-950/50 dark:peer-checked:text-indigo-400 dark:peer-checked:border-indigo-600 transition-all hover:border-indigo-300">
                    <i class="fa-solid {{ $labelOption === 'Kantor' ? 'fa-building' : ($labelOption === 'Kost' ? 'fa-house-chimney-user' : ($labelOption === 'Lainnya' ? 'fa-map-pin' : 'fa-house')) }} text-[10px]"></i>
                    {{ $labelOption }}
                </span>
            </label>
            @endforeach
        </div>
        @error('label') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- ── Recipient name + Phone ── --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">
                Nama Penerima <span class="text-rose-500">*</span>
            </label>
            <input type="text" name="recipient_name" value="{{ $val('recipient_name') }}"
                   placeholder="Nama lengkap penerima" required
                   class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm text-slate-800 dark:text-slate-200 placeholder-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 transition-all">
            @error('recipient_name') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">
                Nomor HP <span class="text-rose-500">*</span>
            </label>
            <input type="tel" name="phone" value="{{ $val('phone') }}"
                   placeholder="08xx-xxxx-xxxx" required
                   class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm text-slate-800 dark:text-slate-200 placeholder-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 transition-all">
            @error('phone') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
        </div>
    </div>

    {{-- ── Full Address ── --}}
    <div>
        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">
            Alamat Lengkap <span class="text-rose-500">*</span>
        </label>
        <textarea name="address" rows="2"
                  placeholder="Nama jalan, nomor rumah, RT/RW, kelurahan, kecamatan..."
                  required
                  class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm text-slate-800 dark:text-slate-200 placeholder-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 transition-all resize-none">{{ $val('address') }}</textarea>
        @error('address') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- ── Province dropdown (RajaOngkir) ── --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">
                Provinsi <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <select name="province"
                        x-model="selectedProvince"
                        @change="onProvinceChange($event.target.value)"
                        required
                        :disabled="loadingProvinces"
                        class="w-full appearance-none rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2.5 pr-10 text-sm text-slate-800 dark:text-slate-200 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 transition-all disabled:opacity-60 disabled:cursor-wait">
                    <option value="" x-show="!loadingProvinces">— Pilih Provinsi —</option>
                    <option value="" x-show="loadingProvinces" disabled>Memuat provinsi...</option>
                    <template x-for="p in provinces" :key="p.province_id">
                        <option :value="p.province_id + '|' + p.province"
                                x-text="p.province"
                                :selected="selectedProvince === p.province_id + '|' + p.province">
                        </option>
                    </template>
                </select>
                <div class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-slate-400">
                    <i x-show="!loadingProvinces" class="fa-solid fa-chevron-down text-xs"></i>
                    <i x-show="loadingProvinces" x-cloak class="fa-solid fa-circle-notch fa-spin text-xs text-indigo-500"></i>
                </div>
            </div>
            @error('province') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- ── City dropdown (dynamic, depends on province) ── --}}
        <div>
            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">
                Kota / Kabupaten <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <select name="city"
                        x-model="selectedCity"
                        required
                        :disabled="loadingCities || cities.length === 0"
                        class="w-full appearance-none rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2.5 pr-10 text-sm text-slate-800 dark:text-slate-200 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 transition-all disabled:opacity-60 disabled:cursor-not-allowed">
                    <option value="" x-show="!loadingCities && cities.length === 0">— Pilih provinsi dulu —</option>
                    <option value="" x-show="loadingCities" disabled>Memuat kota...</option>
                    <option value="" x-show="!loadingCities && cities.length > 0">— Pilih Kota —</option>
                    <template x-for="c in cities" :key="c.city_id">
                        <option :value="c.city_id + '|' + c.city_name"
                                x-text="c.city_name"
                                :selected="selectedCity === c.city_id + '|' + c.city_name">
                        </option>
                    </template>
                </select>
                <div class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-slate-400">
                    <i x-show="!loadingCities" class="fa-solid fa-chevron-down text-xs"></i>
                    <i x-show="loadingCities" x-cloak class="fa-solid fa-circle-notch fa-spin text-xs text-indigo-500"></i>
                </div>
            </div>
            @error('city') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
        </div>
    </div>

    {{-- ── Postal Code ── --}}
    <div class="sm:w-1/3">
        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">
            Kode Pos <span class="text-rose-500">*</span>
        </label>
        <input type="text" name="postal_code" value="{{ $val('postal_code') }}"
               placeholder="12345" maxlength="10" required
               class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm text-slate-800 dark:text-slate-200 placeholder-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 transition-all">
        @error('postal_code') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- ── Set as Default toggle ── --}}
    <label class="flex items-center gap-3 cursor-pointer select-none group">
        <div class="relative">
            <input type="checkbox" name="is_default" value="1"
                   {{ $val('is_default') ? 'checked' : '' }}
                   class="peer sr-only">
            <div class="h-5 w-9 rounded-full border-2 border-slate-300 bg-slate-200 dark:border-slate-600 dark:bg-slate-700 peer-checked:border-indigo-500 peer-checked:bg-indigo-500 transition-all"></div>
            <div class="absolute left-0.5 top-0.5 h-4 w-4 rounded-full bg-white shadow-sm transition-all peer-checked:translate-x-4"></div>
        </div>
        <span class="text-sm text-slate-600 dark:text-slate-400 group-hover:text-slate-800 dark:group-hover:text-slate-200 transition-colors">
            Jadikan alamat utama
        </span>
    </label>

</div>
