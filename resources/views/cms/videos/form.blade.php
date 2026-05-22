@extends('layouts.cms')

@section('title', isset($video) ? 'Edit Video — DevGate' : 'Tambah Video — DevGate')

@section('breadcrumbs')
<div class="flex items-center gap-2 text-xs font-semibold text-slate-400">
    <span>Portal CMS</span>
    <i class="fa-solid fa-chevron-right text-[8px]"></i>
    <a href="{{ route('cms.videos.index') }}" class="hover:text-white transition-colors">Video Tutorial</a>
    <i class="fa-solid fa-chevron-right text-[8px]"></i>
    <span class="text-white">{{ isset($video) ? 'Edit Video' : 'Tambah Video' }}</span>
</div>
@endsection

@section('content')
<div class="max-w-2xl mx-auto space-y-6" x-data="{
    platform: '{{ old('platform', $video?->platform ?? 'youtube') }}',
    videoId: '{{ old('video_id', $video?->video_id ?? '') }}',
    get previewUrl() {
        if (this.platform === 'youtube' && this.videoId) {
            return 'https://i.ytimg.com/vi/' + this.videoId + '/hqdefault.jpg';
        }
        return '';
    },
    get watchUrl() {
        if (this.platform === 'youtube' && this.videoId) {
            return 'https://www.youtube.com/watch?v=' + this.videoId;
        }
        return '';
    }
}">

    {{-- Page Header --}}
    <div class="border-b border-slate-800/60 pb-6">
        <h1 class="text-2xl font-extrabold text-white tracking-tight flex items-center gap-3">
            <i class="fa-solid fa-{{ isset($video) ? 'pen-to-square' : 'circle-plus' }} text-indigo-500"></i>
            {{ isset($video) ? 'Edit Video' : 'Tambah Video Tutorial' }}
        </h1>
        <p class="text-slate-400 text-xs mt-1">
            {{ isset($video) ? 'Ubah detail video tutorial yang ditampilkan di halaman blog.' : 'Tambah video YouTube atau TikTok ke halaman blog.' }}
        </p>
    </div>

    {{-- Form Card --}}
    <div class="rounded-3xl border border-slate-800/80 bg-slate-950/45 p-6 shadow-xl">
        <form
            method="POST"
            action="{{ isset($video) ? route('cms.videos.update', $video) : route('cms.videos.store') }}"
            class="space-y-6"
        >
            @csrf
            @if(isset($video)) @method('PUT') @endif

            {{-- Platform selector --}}
            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Platform</label>
                <div class="flex gap-3">
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="platform" value="youtube"
                               x-model="platform" class="peer sr-only">
                        <div class="flex items-center gap-3 rounded-2xl border border-slate-700 bg-slate-900 px-4 py-3 transition-all peer-checked:border-rose-500 peer-checked:bg-rose-500/10">
                            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-rose-600/20 text-rose-400 shrink-0">
                                <i class="fa-brands fa-youtube text-lg"></i>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-white">YouTube</p>
                                <p class="text-[10px] text-slate-500">Video panjang / tutorial</p>
                            </div>
                        </div>
                    </label>
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="platform" value="tiktok"
                               x-model="platform" class="peer sr-only">
                        <div class="flex items-center gap-3 rounded-2xl border border-slate-700 bg-slate-900 px-4 py-3 transition-all peer-checked:border-slate-400 peer-checked:bg-white/5">
                            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-white/5 border border-white/10 text-white shrink-0">
                                <i class="fa-brands fa-tiktok text-lg"></i>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-white">TikTok</p>
                                <p class="text-[10px] text-slate-500">Short video / tips cepat</p>
                            </div>
                        </div>
                    </label>
                </div>
                @error('platform') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Video ID + Live Preview --}}
            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">
                    <span x-show="platform === 'youtube'">Video ID YouTube</span>
                    <span x-show="platform === 'tiktok'" x-cloak>URL Video TikTok</span>
                    <span class="text-rose-500">*</span>
                </label>
                <div class="flex gap-3 items-start">
                    <div class="flex-grow">
                        <input type="text" name="video_id"
                               x-model="videoId"
                               :placeholder="platform === 'youtube' ? 'Contoh: dQw4w9WgXcQ' : 'Contoh: https://www.tiktok.com/@username/video/7380919256498258177'"
                               required
                               class="w-full rounded-xl border border-slate-700 bg-slate-900 px-4 py-2.5 text-sm text-white placeholder-slate-600 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 transition-all font-mono">
                        <div class="mt-1.5 space-y-0.5">
                            <p x-show="platform === 'youtube'" class="text-[10px] text-slate-600">
                                Salin dari URL: youtube.com/watch?v=<strong class="text-slate-400">VIDEO_ID</strong>
                            </p>
                            <p x-show="platform === 'tiktok'" x-cloak class="text-[10px] text-amber-500/80">
                                <i class="fa-solid fa-triangle-exclamation text-[9px]"></i>
                                Tempel <strong>URL lengkap</strong> video TikTok — bukan hanya angka ID-nya, karena TikTok membutuhkan <code class="text-slate-400">@username</code> di URL.
                            </p>
                        </div>
                    </div>
                    {{-- YouTube Thumbnail Preview --}}
                    <div x-show="platform === 'youtube' && videoId" x-cloak class="shrink-0">
                        <div class="relative h-16 w-28 rounded-xl overflow-hidden bg-slate-900 border border-slate-700">
                            <img :src="previewUrl" alt="Preview"
                                 class="h-full w-full object-cover"
                                 onerror="this.style.display='none'">
                            <a :href="watchUrl" target="_blank"
                               class="absolute inset-0 flex items-center justify-center bg-black/30 hover:bg-black/50 transition-colors">
                                <i class="fa-solid fa-play text-white text-xs"></i>
                            </a>
                        </div>
                        <p class="text-[9px] text-slate-600 text-center mt-1">Preview thumbnail</p>
                    </div>
                </div>
                @error('video_id') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Title --}}
            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">
                    Judul Video <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="title"
                       value="{{ old('title', $video?->title) }}"
                       placeholder="Judul video yang menarik..."
                       required
                       class="w-full rounded-xl border border-slate-700 bg-slate-900 px-4 py-2.5 text-sm text-white placeholder-slate-600 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 transition-all">
                @error('title') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Description --}}
            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Deskripsi Singkat</label>
                <textarea name="description" rows="2"
                          placeholder="Ringkasan singkat isi video..."
                          class="w-full rounded-xl border border-slate-700 bg-slate-900 px-4 py-2.5 text-sm text-white placeholder-slate-600 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 transition-all resize-none">{{ old('description', $video?->description) }}</textarea>
                @error('description') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Duration + Views + Sort Order --}}
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Durasi</label>
                    <input type="text" name="duration"
                           value="{{ old('duration', $video?->duration) }}"
                           placeholder="12:34"
                           class="w-full rounded-xl border border-slate-700 bg-slate-900 px-4 py-2.5 text-sm text-white placeholder-slate-600 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 transition-all">
                    @error('duration') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Views / Likes</label>
                    <input type="text" name="views"
                           value="{{ old('views', $video?->views) }}"
                           placeholder="18.4K"
                           class="w-full rounded-xl border border-slate-700 bg-slate-900 px-4 py-2.5 text-sm text-white placeholder-slate-600 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 transition-all">
                    @error('views') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Urutan</label>
                    <input type="number" name="sort_order" min="0"
                           value="{{ old('sort_order', $video?->sort_order ?? 0) }}"
                           class="w-full rounded-xl border border-slate-700 bg-slate-900 px-4 py-2.5 text-sm text-white placeholder-slate-600 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 transition-all">
                    @error('sort_order') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Active Toggle --}}
            <label class="flex items-center gap-3 cursor-pointer select-none group rounded-2xl border border-slate-800 bg-slate-900/50 px-4 py-3">
                <div class="relative">
                    <input type="checkbox" name="is_active" value="1"
                           {{ old('is_active', $video?->is_active ?? true) ? 'checked' : '' }}
                           class="peer sr-only">
                    <div class="h-5 w-9 rounded-full border-2 border-slate-600 bg-slate-700 peer-checked:border-indigo-500 peer-checked:bg-indigo-500 transition-all"></div>
                    <div class="absolute left-0.5 top-0.5 h-4 w-4 rounded-full bg-white shadow-sm transition-all peer-checked:translate-x-4"></div>
                </div>
                <div>
                    <p class="text-sm font-semibold text-white">Tampilkan di halaman blog</p>
                    <p class="text-xs text-slate-500">Jika dinonaktifkan, video tidak akan tampil di blog.</p>
                </div>
            </label>

            {{-- Actions --}}
            <div class="flex items-center gap-3 pt-2 border-t border-slate-800/60">
                <button type="submit"
                        class="inline-flex items-center gap-2 rounded-2xl bg-indigo-600 px-6 py-2.5 text-sm font-bold text-white hover:bg-indigo-500 transition-all shadow-lg shadow-indigo-600/25 active:scale-95">
                    <i class="fa-solid fa-floppy-disk"></i>
                    {{ isset($video) ? 'Simpan Perubahan' : 'Tambah Video' }}
                </button>
                <a href="{{ route('cms.videos.index') }}"
                   class="text-sm text-slate-400 hover:text-slate-200 transition-colors px-2">
                    Batal
                </a>
            </div>
        </form>
    </div>

</div>
@endsection
