@extends('layouts.cms')

@section('title', 'Kelola Firmware — DevGate')

@section('breadcrumbs')
<div class="flex items-center gap-2 text-xs font-semibold text-slate-400">
    <span>Portal CMS</span>
    <i class="fa-solid fa-chevron-right text-[8px]"></i>
    <span>Firmware</span>
    <i class="fa-solid fa-chevron-right text-[8px]"></i>
    <span class="text-white">Daftar Projek</span>
</div>
@endsection

@section('content')
<div class="space-y-6">

    <!-- Page Header & Action Controls -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between border-b border-slate-800/60 pb-6 gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight flex items-center gap-3">
                <i class="fa-solid fa-microchip text-indigo-500"></i> Kelola Projek Firmware
            </h1>
            <p class="text-slate-400 text-xs mt-1">Buat projek firmware, konfigurasikan tipe chip, dan unggah berkas biner (.bin/.hex) untuk flasher publik.</p>
        </div>

        <div class="flex items-center gap-3 self-end md:self-auto">
            <a 
                href="{{ route('cms.firmware-projects.create') }}" 
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-indigo-600 hover:bg-indigo-500 text-xs font-bold text-white shadow-lg shadow-indigo-600/25 transition-all"
            >
                <i class="fa-solid fa-plus text-[10px]"></i> Buat Projek Baru
            </a>
        </div>
    </div>

    <!-- Projects Table Card -->
    <div class="rounded-3xl border border-slate-800/80 bg-slate-950/45 overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm border-collapse">
                <thead>
                    <tr class="text-[10px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-800/60 bg-slate-950/60">
                        <th class="py-4 px-6">Projek Firmware</th>
                        <th class="py-4 px-6">Tipe Chip / Device</th>
                        <th class="py-4 px-6 text-center">Jumlah Versi</th>
                        <th class="py-4 px-6 text-center">Terakhir Diperbarui</th>
                        <th class="py-4 px-6 text-center">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-850">
                    @forelse($projects as $project)
                        <tr class="group hover:bg-slate-900/20 transition-all">
                            <!-- Name & Slug -->
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 shrink-0">
                                        <i class="fa-solid fa-code-commit text-sm"></i>
                                    </div>
                                    <div class="overflow-hidden">
                                        <h3 class="text-xs sm:text-sm font-bold text-white group-hover:text-indigo-400 transition-colors leading-snug truncate" title="{{ $project->name }}">
                                            {{ $project->name }}
                                        </h3>
                                        <p class="text-[10px] text-slate-500 font-mono mt-1">
                                            slug: {{ $project->slug }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            <!-- Device Type / Chip -->
                            <td class="py-4 px-6">
                                <span class="inline-flex items-center rounded-lg border border-slate-700 bg-slate-850 px-2 py-0.5 text-[10px] font-bold text-indigo-300 font-mono">
                                    <i class="fa-solid fa-microchip mr-1.5 text-[8px]"></i>{{ $project->device_type }}
                                </span>
                            </td>

                            <!-- Total Versions -->
                            <td class="py-4 px-6 text-center">
                                <span class="inline-flex h-6 min-w-[24px] items-center justify-center rounded-full bg-slate-900 border border-slate-800 text-xs font-mono font-bold text-slate-300 px-1">
                                    {{ $project->files_count }}
                                </span>
                            </td>

                            <!-- Last Updated -->
                            <td class="py-4 px-6 text-center text-slate-400 text-xs font-mono">
                                {{ $project->updated_at->format('d M Y, H:i') }}
                            </td>

                            <!-- Actions -->
                            <td class="py-4 px-6">
                                <div class="flex items-center justify-center gap-2">
                                    <a 
                                        href="{{ route('cms.firmware-projects.show', $project->id) }}" 
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-850 bg-slate-900 text-xs font-bold text-indigo-400 hover:bg-indigo-600 hover:border-indigo-500 hover:text-white transition-all shadow-sm"
                                    >
                                        <i class="fa-solid fa-folder-open text-[10px]"></i> Detail & File
                                    </a>

                                    <a 
                                        href="{{ route('cms.firmware-projects.edit', $project->id) }}" 
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-850 bg-slate-900 text-xs font-bold text-slate-355 hover:bg-slate-800 hover:text-white transition-all shadow-sm"
                                    >
                                        <i class="fa-solid fa-pen-to-square text-[10px]"></i> Edit
                                    </a>

                                    <form action="{{ route('cms.firmware-projects.destroy', $project->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus projek ini? Seluruh file biner yang telah diunggah akan dihapus permanen dari server.')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button 
                                            type="submit" 
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-850 bg-slate-900 text-xs font-bold text-rose-400 hover:bg-rose-600 hover:border-rose-500 hover:text-white transition-all shadow-sm"
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
                                <i class="fa-solid fa-microchip text-5xl text-slate-700 mb-4 block animate-pulse"></i>
                                <span class="text-sm font-bold block text-slate-400 mb-1">Belum Ada Projek Firmware</span>
                                <span class="text-xs text-slate-650">Klik tombol "Buat Projek Baru" di atas untuk mulai menambahkan projek pertama Anda.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
