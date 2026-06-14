@extends('layouts.cms')

@section('title', 'Buat Projek Firmware — DevGate')

@section('breadcrumbs')
<div class="flex items-center gap-2 text-xs font-semibold text-slate-400">
    <span>Portal CMS</span>
    <i class="fa-solid fa-chevron-right text-[8px]"></i>
    <span>Firmware</span>
    <i class="fa-solid fa-chevron-right text-[8px]"></i>
    <a href="{{ route('cms.firmware-projects.index') }}" class="hover:text-white transition-colors">Daftar Projek</a>
    <i class="fa-solid fa-chevron-right text-[8px]"></i>
    <span class="text-white">Buat Projek</span>
</div>
@endsection

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- Page Header & Action Controls -->
    <div class="flex items-center justify-between border-b border-slate-800/60 pb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight flex items-center gap-3">
                <i class="fa-solid fa-plus-circle text-indigo-500"></i> Buat Projek Firmware Baru
            </h1>
            <p class="text-slate-400 text-xs mt-1">Definisikan projek firmware baru dan tipe mikrokontroler target.</p>
        </div>
        <a 
            href="{{ route('cms.firmware-projects.index') }}" 
            class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border border-slate-800 bg-slate-900 text-xs font-bold text-slate-300 hover:bg-slate-800 hover:text-white transition-all shadow-sm"
        >
            <i class="fa-solid fa-arrow-left text-[10px]"></i> Kembali
        </a>
    </div>

    <!-- Form Card -->
    <div class="rounded-3xl border border-slate-800/80 bg-slate-950/45 p-6 md:p-8 shadow-xl">
        <form action="{{ route('cms.firmware-projects.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Name -->
            <div class="space-y-2">
                <label for="name" class="block text-xs font-bold text-slate-300 uppercase tracking-wider">Nama Projek Firmware</label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    value="{{ old('name') }}" 
                    required 
                    placeholder="Contoh: DevGate Smart Switch ESP32" 
                    class="w-full rounded-2xl border border-slate-800 bg-slate-900/60 px-4 py-3 text-sm text-white focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 @error('name') border-rose-500 @enderror"
                >
                @error('name')
                    <p class="text-xs text-rose-450 mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- Device Type / Chip Family -->
            <div class="space-y-2">
                <label for="device_type" class="block text-xs font-bold text-slate-300 uppercase tracking-wider">Tipe Chip / Perangkat</label>
                <select 
                    id="device_type" 
                    name="device_type" 
                    required
                    class="w-full rounded-2xl border border-slate-800 bg-slate-900/60 px-4 py-3 text-sm text-white focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 @error('device_type') border-rose-500 @enderror"
                >
                    <option value="" disabled {{ old('device_type') ? '' : 'selected' }}>Pilih Tipe Mikrokontroler</option>
                    <option value="ESP32" {{ old('device_type') == 'ESP32' ? 'selected' : '' }}>ESP32 (Standard)</option>
                    <option value="ESP32-S3" {{ old('device_type') == 'ESP32-S3' ? 'selected' : '' }}>ESP32-S3</option>
                    <option value="ESP32-C3" {{ old('device_type') == 'ESP32-C3' ? 'selected' : '' }}>ESP32-C3</option>
                    <option value="ESP8266" {{ old('device_type') == 'ESP8266' ? 'selected' : '' }}>ESP8266</option>
                    <option value="Arduino/Other" {{ old('device_type') == 'Arduino/Other' ? 'selected' : '' }}>Arduino / Board Lainnya</option>
                </select>
                @error('device_type')
                    <p class="text-xs text-rose-450 mt-1 font-semibold">{{ $message }}</p>
                @enderror
                <p class="text-[10px] text-slate-500">Penting untuk pencarian otomatis baudrate dan flash offset pada halaman flasher.</p>
            </div>

            <!-- Description -->
            <div class="space-y-2">
                <label for="description" class="block text-xs font-bold text-slate-300 uppercase tracking-wider">Deskripsi Projek</label>
                <textarea 
                    id="description" 
                    name="description" 
                    rows="4" 
                    placeholder="Tuliskan detail mengenai fungsi projek firmware ini, skema kabel, atau cara penggunaan..." 
                    class="w-full rounded-2xl border border-slate-800 bg-slate-900/60 px-4 py-3 text-sm text-white focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 @error('description') border-rose-500 @enderror"
                >{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-xs text-rose-450 mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-3 border-t border-slate-800/60 pt-6">
                <button 
                    type="reset" 
                    class="px-5 py-2.5 rounded-2xl border border-slate-800 bg-slate-900 text-xs font-bold text-slate-400 hover:bg-slate-800 hover:text-white transition-all"
                >
                    Reset
                </button>
                <button 
                    type="submit" 
                    class="px-5 py-2.5 rounded-2xl bg-indigo-600 hover:bg-indigo-500 text-xs font-bold text-white shadow-lg shadow-indigo-600/20 transition-all"
                >
                    Simpan Projek
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
