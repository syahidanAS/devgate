@extends('layouts.cms')

@section('title', 'Kelola Video Tutorial — DevGate')

@section('breadcrumbs')
<div class="flex items-center gap-2 text-xs font-semibold text-slate-400">
    <span>Portal CMS</span>
    <i class="fa-solid fa-chevron-right text-[8px]"></i>
    <span class="text-white">Video Tutorial</span>
</div>
@endsection

@section('content')
<div class="space-y-6">

    {{-- ── Page Header ── --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between border-b border-slate-800/60 pb-6 gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight flex items-center gap-3">
                <i class="fa-solid fa-clapperboard text-indigo-500"></i> Kelola Video Tutorial
            </h1>
            <p class="text-slate-400 text-xs mt-1">Tambah, edit, atur urutan video YouTube dan TikTok yang tampil di halaman blog.</p>
        </div>
        <a href="{{ route('cms.videos.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-indigo-600 hover:bg-indigo-500 text-xs font-bold text-white shadow-lg shadow-indigo-600/25 transition-all self-end md:self-auto">
            <i class="fa-solid fa-plus text-[10px]"></i> Tambah Video
        </a>
    </div>

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- YOUTUBE TABLE                                             --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div class="rounded-3xl border border-slate-800/80 bg-slate-950/45 overflow-hidden shadow-xl">
        <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-800/60 bg-slate-950/60">
            <div class="flex h-8 w-8 items-center justify-center rounded-xl bg-rose-600/20 text-rose-400">
                <i class="fa-brands fa-youtube"></i>
            </div>
            <div>
                <h2 class="text-sm font-bold text-white">YouTube</h2>
                <p class="text-[10px] text-slate-500">{{ $youtubeVideos->count() }} video</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm border-collapse">
                <thead>
                    <tr class="text-[10px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-800/60">
                        <th class="py-3 px-6">Urutan</th>
                        <th class="py-3 px-6">Thumbnail</th>
                        <th class="py-3 px-6">Judul & Video ID</th>
                        <th class="py-3 px-6">Durasi / Views</th>
                        <th class="py-3 px-6">Status</th>
                        <th class="py-3 px-6 text-center">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/40">
                    @forelse($youtubeVideos as $video)
                    <tr class="group hover:bg-slate-900/20 transition-all">

                        {{-- Sort order --}}
                        <td class="py-4 px-6">
                            <div class="flex flex-col items-center gap-1">
                                <form method="POST" action="{{ route('cms.videos.moveUp', $video) }}">@csrf @method('PATCH')
                                    <button type="submit" class="p-1 text-slate-600 hover:text-white transition-colors" title="Pindah ke atas">
                                        <i class="fa-solid fa-chevron-up text-xs"></i>
                                    </button>
                                </form>
                                <span class="text-xs font-bold text-slate-400 w-6 text-center">{{ $video->sort_order }}</span>
                                <form method="POST" action="{{ route('cms.videos.moveDown', $video) }}">@csrf @method('PATCH')
                                    <button type="submit" class="p-1 text-slate-600 hover:text-white transition-colors" title="Pindah ke bawah">
                                        <i class="fa-solid fa-chevron-down text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>

                        {{-- Thumbnail --}}
                        <td class="py-4 px-6">
                            <div class="relative h-14 w-24 rounded-xl overflow-hidden bg-slate-900 border border-slate-800 shrink-0">
                                <img src="https://i.ytimg.com/vi/{{ $video->video_id }}/mqdefault.jpg"
                                     alt="{{ $video->title }}"
                                     class="h-full w-full object-cover"
                                     onerror="this.parentElement.innerHTML='<div class=\'flex h-full items-center justify-center text-rose-600\'><i class=\'fa-brands fa-youtube text-xl\'></i></div>'">
                                <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity bg-black/40">
                                    <i class="fa-solid fa-play text-white text-xs"></i>
                                </div>
                            </div>
                        </td>

                        {{-- Title & ID --}}
                        <td class="py-4 px-6 max-w-xs">
                            <p class="text-sm font-semibold text-white line-clamp-2 leading-snug">{{ $video->title }}</p>
                            <a href="https://youtube.com/watch?v={{ $video->video_id }}" target="_blank"
                               class="inline-flex items-center gap-1 text-[10px] font-mono text-indigo-400 hover:text-indigo-300 mt-1 transition-colors">
                                <i class="fa-solid fa-link text-[8px]"></i> {{ $video->video_id }}
                            </a>
                        </td>

                        {{-- Duration / Views --}}
                        <td class="py-4 px-6 text-slate-400 text-xs space-y-1">
                            <div class="flex items-center gap-1.5">
                                <i class="fa-regular fa-clock text-[10px]"></i>
                                <span>{{ $video->duration ?? '—' }}</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <i class="fa-regular fa-eye text-[10px]"></i>
                                <span>{{ $video->views ?? '—' }}</span>
                            </div>
                        </td>

                        {{-- Status --}}
                        <td class="py-4 px-6">
                            <form method="POST" action="{{ route('cms.videos.toggleActive', $video) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="group/toggle">
                                    @if($video->is_active)
                                        <span class="inline-flex items-center gap-1.5 rounded-lg border border-emerald-500/20 bg-emerald-500/10 px-2.5 py-1 text-[9px] font-bold uppercase text-emerald-400 hover:bg-rose-500/10 hover:border-rose-500/20 hover:text-rose-400 transition-all">
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                            <span class="group-hover/toggle:hidden">Aktif</span>
                                            <span class="hidden group-hover/toggle:inline">Nonaktifkan</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 rounded-lg border border-slate-700 bg-slate-800/40 px-2.5 py-1 text-[9px] font-bold uppercase text-slate-500 hover:bg-emerald-500/10 hover:border-emerald-500/20 hover:text-emerald-400 transition-all">
                                            <span class="h-1.5 w-1.5 rounded-full bg-slate-600"></span>
                                            <span class="group-hover/toggle:hidden">Nonaktif</span>
                                            <span class="hidden group-hover/toggle:inline">Aktifkan</span>
                                        </span>
                                    @endif
                                </button>
                            </form>
                        </td>

                        {{-- Actions --}}
                        <td class="py-4 px-6">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('cms.videos.edit', $video) }}"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-800 bg-slate-900 text-xs font-bold text-slate-300 hover:bg-indigo-600 hover:border-indigo-500 hover:text-white transition-all shadow-sm">
                                    <i class="fa-solid fa-pen-to-square text-[10px]"></i> Edit
                                </a>
                                <form action="{{ route('cms.videos.destroy', $video) }}" method="POST"
                                      onsubmit="return confirm('Hapus video ini?')" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-800 bg-slate-900 text-xs font-bold text-rose-400 hover:bg-rose-600 hover:border-rose-500 hover:text-white transition-all shadow-sm">
                                        <i class="fa-solid fa-trash-can text-[10px]"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-slate-500">
                            <i class="fa-brands fa-youtube text-4xl text-rose-900/50 mb-3 block"></i>
                            <p class="text-sm font-bold text-slate-400">Belum ada video YouTube</p>
                            <p class="text-xs text-slate-600 mt-1">Klik "Tambah Video" di atas untuk menambahkan.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- TIKTOK TABLE                                              --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div class="rounded-3xl border border-slate-800/80 bg-slate-950/45 overflow-hidden shadow-xl">
        <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-800/60 bg-slate-950/60">
            <div class="flex h-8 w-8 items-center justify-center rounded-xl bg-white/5 text-white border border-white/10">
                <i class="fa-brands fa-tiktok text-sm"></i>
            </div>
            <div>
                <h2 class="text-sm font-bold text-white">TikTok</h2>
                <p class="text-[10px] text-slate-500">{{ $tiktokVideos->count() }} video</p>
            </div>
            @if($tiktokVideos->where('thumbnail_url', null)->count() > 0)
                <span class="ml-auto inline-flex items-center gap-1 text-[10px] font-semibold text-amber-400 bg-amber-500/10 border border-amber-500/20 rounded-lg px-2 py-1">
                    <i class="fa-solid fa-triangle-exclamation text-[9px]"></i>
                    {{ $tiktokVideos->where('thumbnail_url', null)->count() }} video belum ada thumbnail
                </span>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm border-collapse">
                <thead>
                    <tr class="text-[10px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-800/60">
                        <th class="py-3 px-6">Urutan</th>
                        <th class="py-3 px-6">Thumbnail</th>
                        <th class="py-3 px-6">Judul & Deskripsi</th>
                        <th class="py-3 px-6">Durasi / Views</th>
                        <th class="py-3 px-6">Status</th>
                        <th class="py-3 px-6 text-center">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/40">
                    @forelse($tiktokVideos as $video)
                    <tr class="group hover:bg-slate-900/20 transition-all">

                        {{-- Sort order --}}
                        <td class="py-4 px-6">
                            <div class="flex flex-col items-center gap-1">
                                <form method="POST" action="{{ route('cms.videos.moveUp', $video) }}">@csrf @method('PATCH')
                                    <button type="submit" class="p-1 text-slate-600 hover:text-white transition-colors">
                                        <i class="fa-solid fa-chevron-up text-xs"></i>
                                    </button>
                                </form>
                                <span class="text-xs font-bold text-slate-400 w-6 text-center">{{ $video->sort_order }}</span>
                                <form method="POST" action="{{ route('cms.videos.moveDown', $video) }}">@csrf @method('PATCH')
                                    <button type="submit" class="p-1 text-slate-600 hover:text-white transition-colors">
                                        <i class="fa-solid fa-chevron-down text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>

                        {{-- Thumbnail preview --}}
                        <td class="py-4 px-6">
                            @if($video->thumbnail_url)
                                <div class="relative h-16 w-9 rounded-lg overflow-hidden bg-slate-900 border border-emerald-600/30 shrink-0">
                                    <img src="{{ $video->thumbnail_url }}" alt="{{ $video->title }}" class="h-full w-full object-cover">
                                    <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity bg-black/40">
                                        <i class="fa-solid fa-play text-white text-[9px]"></i>
                                    </div>
                                </div>
                            @else
                                <div class="flex h-16 w-9 flex-col items-center justify-center rounded-lg bg-slate-900 border border-amber-600/30 shrink-0 gap-1">
                                    <i class="fa-solid fa-image text-[10px] text-amber-600/60"></i>
                                    <span class="text-[7px] text-amber-600/60 font-bold uppercase leading-none">No img</span>
                                </div>
                            @endif
                        </td>

                        {{-- Title & description --}}
                        <td class="py-4 px-6 max-w-xs">
                            <p class="text-sm font-semibold text-white line-clamp-2 leading-snug">{{ $video->title }}</p>
                            @if($video->description)
                                <p class="text-[10px] text-slate-500 line-clamp-1 mt-0.5">{{ $video->description }}</p>
                            @endif
                            <span class="inline-block text-[9px] font-mono text-slate-600 mt-1 truncate max-w-[120px]" title="{{ $video->video_id }}">
                                {{ Str::limit($video->video_id, 35) }}
                            </span>
                        </td>

                        {{-- Duration / Views --}}
                        <td class="py-4 px-6 text-slate-400 text-xs space-y-1">
                            <div class="flex items-center gap-1.5">
                                <i class="fa-regular fa-clock text-[10px]"></i>
                                <span>{{ $video->duration ?? '—' }}</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <i class="fa-solid fa-heart text-[10px] text-rose-400"></i>
                                <span>{{ $video->views ?? '—' }}</span>
                            </div>
                        </td>

                        {{-- Status --}}
                        <td class="py-4 px-6">
                            <form method="POST" action="{{ route('cms.videos.toggleActive', $video) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="group/toggle">
                                    @if($video->is_active)
                                        <span class="inline-flex items-center gap-1.5 rounded-lg border border-emerald-500/20 bg-emerald-500/10 px-2.5 py-1 text-[9px] font-bold uppercase text-emerald-400 hover:bg-rose-500/10 hover:border-rose-500/20 hover:text-rose-400 transition-all">
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                            <span class="group-hover/toggle:hidden">Aktif</span>
                                            <span class="hidden group-hover/toggle:inline">Nonaktifkan</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 rounded-lg border border-slate-700 bg-slate-800/40 px-2.5 py-1 text-[9px] font-bold uppercase text-slate-500 hover:bg-emerald-500/10 hover:border-emerald-500/20 hover:text-emerald-400 transition-all">
                                            <span class="h-1.5 w-1.5 rounded-full bg-slate-600"></span>
                                            <span class="group-hover/toggle:hidden">Nonaktif</span>
                                            <span class="hidden group-hover/toggle:inline">Aktifkan</span>
                                        </span>
                                    @endif
                                </button>
                            </form>
                        </td>

                        {{-- Actions --}}
                        <td class="py-4 px-6">
                            <div class="flex flex-col items-center gap-2">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('cms.videos.edit', $video) }}"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-800 bg-slate-900 text-xs font-bold text-slate-300 hover:bg-indigo-600 hover:border-indigo-500 hover:text-white transition-all shadow-sm">
                                        <i class="fa-solid fa-pen-to-square text-[10px]"></i> Edit
                                    </a>
                                    <form action="{{ route('cms.videos.destroy', $video) }}" method="POST"
                                          onsubmit="return confirm('Hapus video ini?')" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-800 bg-slate-900 text-xs font-bold text-rose-400 hover:bg-rose-600 hover:border-rose-500 hover:text-white transition-all shadow-sm">
                                            <i class="fa-solid fa-trash-can text-[10px]"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                                {{-- Refresh Thumbnail via oEmbed --}}
                                <form method="POST" action="{{ route('cms.videos.refreshThumbnail', $video) }}" class="w-full">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                            title="Ambil ulang thumbnail dari TikTok oEmbed API"
                                            class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-xl border border-cyan-800/50 bg-cyan-900/20 text-[10px] font-bold text-cyan-400 hover:bg-cyan-600 hover:border-cyan-500 hover:text-white transition-all">
                                        <i class="fa-solid fa-rotate text-[9px]"></i>
                                        {{ $video->thumbnail_url ? 'Refresh Thumbnail' : 'Ambil Thumbnail' }}
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-slate-500">
                            <i class="fa-brands fa-tiktok text-4xl text-slate-700 mb-3 block"></i>
                            <p class="text-sm font-bold text-slate-400">Belum ada video TikTok</p>
                            <p class="text-xs text-slate-600 mt-1">Klik "Tambah Video" di atas untuk menambahkan.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
