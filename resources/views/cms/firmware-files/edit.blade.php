@extends('layouts.cms')

@section('title', 'Edit Versi Firmware — DevGate')

@section('breadcrumbs')
<div class="flex items-center gap-2 text-xs font-semibold text-slate-400">
    <span>Portal CMS</span>
    <i class="fa-solid fa-chevron-right text-[8px]"></i>
    <span>Firmware</span>
    <i class="fa-solid fa-chevron-right text-[8px]"></i>
    <a href="{{ route('cms.firmware-projects.index') }}" class="hover:text-white transition-colors">Daftar Projek</a>
    <i class="fa-solid fa-chevron-right text-[8px]"></i>
    <a href="{{ route('cms.firmware-projects.show', $project->id) }}" class="hover:text-white transition-colors">{{ $project->name }}</a>
    <i class="fa-solid fa-chevron-right text-[8px]"></i>
    <span class="text-white">Edit Versi {{ $firmwareFile->version }}</span>
</div>
@endsection

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- Page Header & Action Controls -->
    <div class="flex items-center justify-between border-b border-slate-800/60 pb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight flex items-center gap-3">
                <i class="fa-solid fa-file-pen text-indigo-500"></i> Edit File Firmware
            </h1>
            <p class="text-slate-400 text-xs mt-1">Ubah metadata, catatan rilis, atau ganti file biner (.bin/.hex) untuk versi {{ $firmwareFile->version }} projek '{{ $project->name }}'.</p>
        </div>
        <a 
            href="{{ route('cms.firmware-projects.show', $project->id) }}" 
            class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border border-slate-800 bg-slate-900 text-xs font-bold text-slate-300 hover:bg-slate-800 hover:text-white transition-all shadow-sm"
        >
            <i class="fa-solid fa-arrow-left text-[10px]"></i> Kembali ke Projek
        </a>
    </div>

    <!-- Form Card -->
    <div class="rounded-3xl border border-slate-800/80 bg-slate-950/45 p-6 md:p-8 shadow-xl">
        <form action="{{ route('cms.firmware-files.update', $firmwareFile->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Project Reference (Read-only) -->
            <div class="space-y-2">
                <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Projek Target</span>
                <div class="rounded-2xl border border-slate-900 bg-slate-950 px-4 py-3 text-xs text-slate-400 font-semibold">
                    {{ $project->name }} (Tipe Device: {{ $project->device_type }})
                </div>
            </div>

            <!-- Version -->
            <div class="space-y-2">
                <label for="version" class="block text-xs font-bold text-slate-300 uppercase tracking-wider">Versi Firmware</label>
                <input 
                    type="text" 
                    id="version" 
                    name="version" 
                    value="{{ old('version', $firmwareFile->version) }}" 
                    required 
                    placeholder="v1.0.0" 
                    class="w-full rounded-2xl border border-slate-800 bg-slate-900/60 px-4 py-3 text-sm text-white focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 @error('version') border-rose-500 @enderror"
                >
                @error('version')
                    <p class="text-xs text-rose-450 mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- Flash Offset -->
            <div class="space-y-2">
                <label for="flash_offset" class="block text-xs font-bold text-slate-300 uppercase tracking-wider">Flash Offset (Address Address)</label>
                <input 
                    type="text" 
                    id="flash_offset" 
                    name="flash_offset" 
                    value="{{ old('flash_offset', $firmwareFile->flash_offset) }}" 
                    required 
                    placeholder="0x1000" 
                    class="w-full rounded-2xl border border-slate-800 bg-slate-900/60 px-4 py-3 text-sm text-white focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 @error('flash_offset') border-rose-500 @enderror"
                >
                @error('flash_offset')
                    <p class="text-xs text-rose-450 mt-1 font-semibold">{{ $message }}</p>
                @enderror
                <p class="text-[10px] text-slate-500">Alamat memori target untuk flash. Default ESP32: <code class="font-mono text-indigo-400">0x1000</code>. Default ESP8266/S3/C3: <code class="font-mono text-indigo-400">0x0</code>.</p>
            </div>

            <!-- Current Binary File Info & Optional File Replacement -->
            <div class="space-y-4 p-5 rounded-2xl border border-slate-800/80 bg-slate-900/40">
                <div class="flex items-center justify-between text-xs">
                    <div>
                        <span class="text-slate-500 font-bold block">BERKAS BINER SAAT INI</span>
                        <a href="{{ $firmwareFile->file_url }}" target="_blank" class="font-mono text-cyan-400 hover:underline inline-flex items-center gap-1 font-bold mt-1">
                            <i class="fa-solid fa-file-code text-[10px]"></i> {{ basename($firmwareFile->file_path) }}
                        </a>
                    </div>
                    @php
                        $fileSize = 'Unknown';
                        try {
                            if (Storage::disk('public')->exists($firmwareFile->file_path)) {
                                $bytes = Storage::disk('public')->size($firmwareFile->file_path);
                                if ($bytes >= 1048576) {
                                    $fileSize = number_format($bytes / 1048576, 2) . ' MB';
                                } elseif ($bytes >= 1024) {
                                    $fileSize = number_format($bytes / 1024, 2) . ' KB';
                                } else {
                                    $fileSize = $bytes . ' B';
                                }
                            }
                        } catch (\Exception $e) {}
                    @endphp
                    <div class="text-right">
                        <span class="text-slate-500 font-bold block">UKURAN BERKAS</span>
                        <span class="text-slate-300 font-mono font-bold mt-1 block">{{ $fileSize }}</span>
                    </div>
                </div>

                <div class="h-px bg-slate-800/60"></div>

                <!-- Replace Binary File Input -->
                <div class="space-y-2">
                    <label for="file" class="block text-xs font-bold text-slate-300 uppercase tracking-wider">Ganti Berkas Biner (.bin / .hex)</label>
                    <input 
                        type="file" 
                        id="file" 
                        name="file" 
                        accept=".bin,.hex"
                        class="w-full rounded-xl border border-slate-800 bg-slate-900/60 px-3 py-2 text-xs text-white file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-[10px] file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-500 file:cursor-pointer @error('file') border-rose-500 @enderror"
                    >
                    @error('file')
                        <p class="text-[10px] text-rose-450 mt-0.5 font-semibold">{{ $message }}</p>
                    @enderror
                    <p class="text-[10px] text-slate-500">Biarkan kosong jika Anda tidak ingin mengubah berkas biner saat ini.</p>
                </div>
            </div>

            <!-- Changelog -->
            <div class="space-y-2">
                <label for="changelog" class="block text-xs font-bold text-slate-300 uppercase tracking-wider">Catatan Perubahan (Changelog)</label>
                <textarea 
                    id="changelog" 
                    name="changelog" 
                    rows="4" 
                    placeholder="Tuliskan catatan rilis untuk versi ini..." 
                    class="w-full rounded-2xl border border-slate-800 bg-slate-900/60 px-4 py-3 text-sm text-white focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 @error('changelog') border-rose-500 @enderror"
                >{{ old('changelog', $firmwareFile->changelog) }}</textarea>
                @error('changelog')
                    <p class="text-xs text-rose-450 mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- Active Status -->
            <div class="flex items-center gap-3">
                <input 
                    type="checkbox" 
                    id="is_active" 
                    name="is_active" 
                    value="1" 
                    {{ old('is_active', $firmwareFile->is_active) ? 'checked' : '' }} 
                    class="rounded border-slate-800 bg-slate-900 text-indigo-600 focus:ring-indigo-500"
                >
                <label for="is_active" class="text-xs font-bold text-slate-300 uppercase tracking-wider cursor-pointer">Aktifkan versi firmware ini untuk publik</label>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-3 border-t border-slate-800/60 pt-6">
                <a 
                    href="{{ route('cms.firmware-projects.show', $project->id) }}" 
                    class="px-5 py-2.5 rounded-2xl border border-slate-800 bg-slate-900 text-xs font-bold text-slate-400 hover:bg-slate-800 hover:text-white transition-all text-center"
                >
                    Batal
                </a>
                <button 
                    type="submit" 
                    class="px-5 py-2.5 rounded-2xl bg-indigo-600 hover:bg-indigo-500 text-xs font-bold text-white shadow-lg shadow-indigo-600/20 transition-all"
                >
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
