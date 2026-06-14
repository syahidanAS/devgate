@extends('layouts.cms')

@section('title', 'Detail Projek Firmware — DevGate')

@section('breadcrumbs')
<div class="flex items-center gap-2 text-xs font-semibold text-slate-400">
    <span>Portal CMS</span>
    <i class="fa-solid fa-chevron-right text-[8px]"></i>
    <span>Firmware</span>
    <i class="fa-solid fa-chevron-right text-[8px]"></i>
    <a href="{{ route('cms.firmware-projects.index') }}" class="hover:text-white transition-colors">Daftar Projek</a>
    <i class="fa-solid fa-chevron-right text-[8px]"></i>
    <span class="text-white">{{ $firmwareProject->name }}</span>
</div>
@endsection

@section('content')
<div class="space-y-6">

    <!-- Page Header & Action Controls -->
    <div class="flex items-center justify-between border-b border-slate-800/60 pb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight flex items-center gap-3">
                <i class="fa-solid fa-microchip text-indigo-500"></i> {{ $firmwareProject->name }}
            </h1>
            <p class="text-slate-400 text-xs mt-1">Detail projek firmware, daftar biner terunggah, dan manajemen rilis versi.</p>
        </div>
        <a 
            href="{{ route('cms.firmware-projects.index') }}" 
            class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border border-slate-800 bg-slate-900 text-xs font-bold text-slate-300 hover:bg-slate-800 hover:text-white transition-all shadow-sm"
        >
            <i class="fa-solid fa-arrow-left text-[10px]"></i> Kembali ke Daftar
        </a>
    </div>

    <!-- Main Content Layout (Grid) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left: Project Info Details & Upload Form -->
        <div class="lg:col-span-1 space-y-6">
            
            <!-- Project Info Card -->
            <div class="rounded-3xl border border-slate-800/80 bg-slate-950/45 p-6 shadow-xl space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-sm font-extrabold text-white uppercase tracking-wider">Detail Projek</h2>
                    <span class="inline-flex items-center rounded-lg border border-indigo-500/20 bg-indigo-500/10 px-2.5 py-0.5 text-[10px] font-bold text-indigo-400 font-mono">
                        {{ $firmwareProject->device_type }}
                    </span>
                </div>
                
                <div class="h-px bg-slate-800/60"></div>
                
                <div class="space-y-3 text-xs">
                    <div>
                        <span class="text-slate-500 font-bold block">NAMA PROJEK</span>
                        <span class="text-white font-medium">{{ $firmwareProject->name }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500 font-bold block">SLUG PROJEK</span>
                        <span class="text-slate-400 font-mono">{{ $firmwareProject->slug }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500 font-bold block">DESKRIPSI</span>
                        <p class="text-slate-300 leading-relaxed mt-1">{{ $firmwareProject->description ?: 'Tidak ada deskripsi.' }}</p>
                    </div>
                    <div>
                        <span class="text-slate-500 font-bold block">TANGGAL DIBUAT</span>
                        <span class="text-slate-400 font-mono">{{ $firmwareProject->created_at->format('d M Y, H:i') }}</span>
                    </div>
                </div>

                <div class="h-px bg-slate-800/60 pt-2"></div>

                <div class="flex justify-end">
                    <a 
                        href="{{ route('cms.firmware-projects.edit', $firmwareProject->id) }}"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-slate-800 bg-slate-900 text-xs font-bold text-slate-355 hover:bg-indigo-600 hover:border-indigo-500 hover:text-white transition-all shadow-sm"
                    >
                        <i class="fa-solid fa-pen-to-square text-[10px]"></i> Edit Detail Projek
                    </a>
                </div>
            </div>

            <!-- Upload Version Card -->
            <div class="rounded-3xl border border-slate-800/80 bg-slate-950/45 p-6 shadow-xl space-y-4">
                <h2 class="text-sm font-extrabold text-white uppercase tracking-wider flex items-center gap-2">
                    <i class="fa-solid fa-cloud-arrow-up text-indigo-500"></i> Unggah Firmware Baru
                </h2>
                
                <div class="h-px bg-slate-800/60"></div>

                <form action="{{ route('cms.firmware-files.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <input type="hidden" name="firmware_project_id" value="{{ $firmwareProject->id }}">

                    <!-- Version -->
                    <div class="space-y-1">
                        <label for="version" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Versi (Contoh: v1.0.0)</label>
                        <input 
                            type="text" 
                            id="version" 
                            name="version" 
                            value="{{ old('version') }}" 
                            required 
                            placeholder="v1.0.0" 
                            class="w-full rounded-xl border border-slate-800 bg-slate-900/60 px-3.5 py-2 text-xs text-white focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 @error('version') border-rose-500 @enderror"
                        >
                        @error('version')
                            <p class="text-[10px] text-rose-450 mt-0.5 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Flash Offset -->
                    <div class="space-y-1">
                        <label for="flash_offset" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Flash Offset (Address)</label>
                        <input 
                            type="text" 
                            id="flash_offset" 
                            name="flash_offset" 
                            value="{{ old('flash_offset', ($firmwareProject->device_type === 'ESP8266' || Str::contains($firmwareProject->device_type, ['S3', 'C3'])) ? '0x0' : '0x1000') }}" 
                            required 
                            placeholder="0x1000" 
                            class="w-full rounded-xl border border-slate-800 bg-slate-900/60 px-3.5 py-2 text-xs text-white focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 @error('flash_offset') border-rose-500 @enderror"
                        >
                        @error('flash_offset')
                            <p class="text-[10px] text-rose-450 mt-0.5 font-semibold">{{ $message }}</p>
                        @enderror
                        <p class="text-[9px] text-slate-500">
                            ESP32 standar: <code class="font-mono text-indigo-400">0x1000</code> atau <code class="font-mono text-indigo-400">0x10000</code>. ESP8266 / S3 / C3 / merged bin: <code class="font-mono text-indigo-400">0x0</code>.
                        </p>
                    </div>

                    <!-- File Bin -->
                    <div class="space-y-1">
                        <label for="file" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Berkas Firmware (.bin / .hex)</label>
                        <input 
                            type="file" 
                            id="file" 
                            name="file" 
                            required 
                            accept=".bin,.hex"
                            class="w-full rounded-xl border border-slate-800 bg-slate-900/60 px-3 py-2 text-xs text-white file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-[10px] file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-500 file:cursor-pointer @error('file') border-rose-500 @enderror"
                        >
                        @error('file')
                            <p class="text-[10px] text-rose-450 mt-0.5 font-semibold">{{ $message }}</p>
                        @enderror
                        <p class="text-[9px] text-slate-500">Maksimum ukuran berkas: 16MB. Hanya format .bin atau .hex.</p>
                    </div>

                    <!-- Changelog / Notes -->
                    <div class="space-y-1">
                        <label for="changelog" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Catatan Perubahan (Changelog)</label>
                        <textarea 
                            id="changelog" 
                            name="changelog" 
                            rows="3" 
                            placeholder="Catatan rilis untuk versi ini..." 
                            class="w-full rounded-xl border border-slate-800 bg-slate-900/60 px-3.5 py-2 text-xs text-white focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 @error('changelog') border-rose-500 @enderror"
                        >{{ old('changelog') }}</textarea>
                        @error('changelog')
                            <p class="text-[10px] text-rose-450 mt-0.5 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Active -->
                    <div class="flex items-center gap-2 pt-1">
                        <input 
                            type="checkbox" 
                            id="is_active" 
                            name="is_active" 
                            value="1" 
                            checked 
                            class="rounded border-slate-800 bg-slate-900 text-indigo-600 focus:ring-indigo-500"
                        >
                        <label for="is_active" class="text-[10px] font-bold text-slate-300 uppercase tracking-wider cursor-pointer">Aktifkan Versi Ini Langsung</label>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-2 border-t border-slate-800/60 flex justify-end">
                        <button 
                            type="submit" 
                            class="w-full inline-flex justify-center items-center gap-2 px-4 py-2.5 rounded-2xl bg-indigo-600 hover:bg-indigo-500 text-xs font-bold text-white shadow-lg shadow-indigo-600/20 transition-all"
                        >
                            <i class="fa-solid fa-cloud-arrow-up text-[10px]"></i> Unggah & Rilis
                        </button>
                    </div>
                </form>
            </div>

        </div>

        <!-- Right: List of Uploaded Firmware Versions -->
        <div class="lg:col-span-2 space-y-6">
            
            <div class="rounded-3xl border border-slate-800/80 bg-slate-950/45 overflow-hidden shadow-xl space-y-4">
                
                <div class="p-6 pb-2">
                    <h2 class="text-sm font-extrabold text-white uppercase tracking-wider flex items-center gap-2">
                        <i class="fa-solid fa-clock-rotate-left text-indigo-500"></i> Riwayat Versi Firmware
                    </h2>
                    <p class="text-slate-400 text-[10px] mt-1">Daftar semua versi firmware yang pernah diunggah untuk projek ini.</p>
                </div>

                <div class="h-px bg-slate-800/60 mx-6"></div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead>
                            <tr class="text-[10px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-800/60 bg-slate-950/60">
                                <th class="py-4 px-6">Versi</th>
                                <th class="py-4 px-6 text-center">Offset Address</th>
                                <th class="py-4 px-6 text-center">Ukuran Berkas</th>
                                <th class="py-4 px-6">Status</th>
                                <th class="py-4 px-6 text-center">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-850">
                            @forelse($firmwareProject->files as $file)
                                @php
                                    $fileSize = 'Tidak Ditemukan';
                                    try {
                                        if (Storage::disk('public')->exists($file->file_path)) {
                                            $bytes = Storage::disk('public')->size($file->file_path);
                                            if ($bytes >= 1048576) {
                                                $fileSize = number_format($bytes / 1048576, 2) . ' MB';
                                            } elseif ($bytes >= 1024) {
                                                $fileSize = number_format($bytes / 1024, 2) . ' KB';
                                            } else {
                                                $fileSize = $bytes . ' B';
                                            }
                                        }
                                    } catch (\Exception $e) {
                                        $fileSize = 'Error';
                                    }
                                @endphp
                                <tr class="group hover:bg-slate-900/10 transition-all">
                                    
                                    <!-- Version & Date -->
                                    <td class="py-4 px-6">
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-bold text-white font-mono bg-indigo-500/10 border border-indigo-500/25 px-2 py-0.5 rounded-lg text-indigo-400">
                                                {{ $file->version }}
                                            </span>
                                            <div class="overflow-hidden">
                                                <span class="text-[9px] text-slate-500 font-mono block">Diunggah:</span>
                                                <span class="text-[10px] text-slate-400 font-mono block">{{ $file->created_at->format('d M Y, H:i') }}</span>
                                            </div>
                                        </div>
                                        @if($file->changelog)
                                            <div class="mt-2 text-[10px] bg-slate-900/40 p-2 rounded-xl border border-slate-800/40 text-slate-350 max-w-xs break-all">
                                                <span class="font-bold text-[8px] uppercase tracking-wider text-indigo-500 block mb-0.5">Changelog:</span>
                                                {!! nl2br(e($file->changelog)) !!}
                                            </div>
                                        @endif
                                    </td>

                                    <!-- Flash Offset -->
                                    <td class="py-4 px-6 text-center font-mono text-xs text-white">
                                        {{ $file->flash_offset }}
                                    </td>

                                    <!-- File Size -->
                                    <td class="py-4 px-6 text-center font-mono text-xs text-slate-400">
                                        {{ $fileSize }}
                                    </td>

                                    <!-- Status -->
                                    <td class="py-4 px-6">
                                        <form action="{{ route('cms.firmware-files.toggle', $file->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button 
                                                type="submit" 
                                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-[9px] font-bold uppercase tracking-wider transition-all border {{ $file->is_active ? 'bg-emerald-500/10 border-emerald-500/20 text-emerald-400 hover:bg-emerald-600 hover:text-white hover:border-emerald-500' : 'bg-slate-800 border-slate-700 text-slate-400 hover:bg-indigo-600 hover:text-white hover:border-indigo-500' }}"
                                                title="Klik untuk mengubah status"
                                            >
                                                <span class="h-1.5 w-1.5 rounded-full {{ $file->is_active ? 'bg-emerald-500 animate-pulse' : 'bg-slate-500' }} mr-1"></span>
                                                {{ $file->is_active ? 'Aktif' : 'Non-Aktif' }}
                                            </button>
                                        </form>
                                    </td>

                                    <!-- Actions -->
                                    <td class="py-4 px-6 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <a 
                                                href="{{ $file->file_url }}" 
                                                target="_blank" 
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl border border-slate-850 bg-slate-900 text-xs font-bold text-cyan-400 hover:bg-cyan-600 hover:border-cyan-500 hover:text-white transition-all shadow-sm"
                                                title="Unduh file biner (.bin)"
                                            >
                                                <i class="fa-solid fa-download text-[10px]"></i> Unduh
                                            </a>

                                            <a 
                                                href="{{ route('cms.firmware-files.edit', $file->id) }}" 
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl border border-slate-850 bg-slate-900 text-xs font-bold text-slate-300 hover:bg-slate-850 hover:text-white transition-all shadow-sm"
                                                title="Edit versi ini"
                                            >
                                                <i class="fa-solid fa-pen-to-square text-[10px]"></i> Edit
                                            </a>

                                            <form action="{{ route('cms.firmware-files.destroy', $file->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus firmware versi {{ $file->version }}?')" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button 
                                                    type="submit" 
                                                    class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl border border-slate-850 bg-slate-900 text-xs font-bold text-rose-400 hover:bg-rose-600 hover:border-rose-500 hover:text-white transition-all shadow-sm"
                                                    title="Hapus versi ini"
                                                >
                                                    <i class="fa-solid fa-trash-can text-[10px]"></i> Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-16 text-center text-slate-500">
                                        <i class="fa-solid fa-cloud-arrow-up text-5xl text-slate-800 mb-4 block animate-bounce"></i>
                                        <span class="text-sm font-bold block text-slate-400 mb-1">Belum Ada File Firmware</span>
                                        <span class="text-xs text-slate-500">Silakan unggah versi firmware baru menggunakan form di sebelah kiri.</span>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>

        </div>

    </div>

</div>
@endsection
