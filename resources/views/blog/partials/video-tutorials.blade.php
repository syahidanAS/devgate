@php
    use App\Models\Video;
    $youtubeVideos  = Video::youtube()->active()->ordered()->get();
    $tiktokVideos   = Video::tiktok()->active()->ordered()->get();
    $youtubeChannel = config('videos.youtube_channel_url', '#');
    $tiktokProfile  = config('videos.tiktok_profile_url', '#');

    // Hide entire section if no videos exist
    if ($youtubeVideos->isEmpty() && $tiktokVideos->isEmpty()) return;
@endphp

<section class="py-16" id="tutorial-videos">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        {{-- ── Section Header ── --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-end justify-between gap-4 mb-10">
            <div>
                <span class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-indigo-600 dark:text-indigo-400 mb-3">
                    <span class="h-1.5 w-1.5 rounded-full bg-indigo-600 dark:bg-indigo-400 animate-ping"></span>
                    Tutorial Video
                </span>
                <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                    Belajar Lewat <span class="bg-gradient-to-r from-indigo-600 to-violet-600 bg-clip-text text-transparent">Video Tutorial</span>
                </h2>
                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400 max-w-xl">
                    Tutorial IoT, Embedded System, dan Web Development — tersedia gratis di YouTube & TikTok.
                </p>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <a href="{{ $youtubeChannel }}" target="_blank" rel="noopener"
                   class="inline-flex items-center gap-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2 text-sm font-semibold text-slate-700 dark:text-slate-300 hover:border-rose-300 hover:text-rose-600 dark:hover:border-rose-700 dark:hover:text-rose-400 transition-all shadow-sm">
                    <i class="fa-brands fa-youtube text-rose-600 dark:text-rose-500"></i> YouTube
                </a>
                <a href="{{ $tiktokProfile }}" target="_blank" rel="noopener"
                   class="inline-flex items-center gap-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2 text-sm font-semibold text-slate-700 dark:text-slate-300 hover:border-slate-400 hover:text-slate-900 dark:hover:text-white transition-all shadow-sm">
                    <i class="fa-brands fa-tiktok text-slate-900 dark:text-white"></i> TikTok
                </a>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════════ --}}
        {{-- YOUTUBE SECTION                                           --}}
        {{-- ══════════════════════════════════════════════════════════ --}}
        @if($youtubeVideos->isNotEmpty())
        <div class="mb-14">
            <div class="flex items-center gap-3 mb-6">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-rose-600 text-white shadow-md shadow-rose-600/30">
                    <i class="fa-brands fa-youtube text-lg"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">YouTube — Tutorial Lengkap</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Video mendalam dengan penjelasan step-by-step</p>
                </div>
                <a href="{{ $youtubeChannel }}" target="_blank" rel="noopener"
                   class="ml-auto text-xs font-semibold text-rose-600 dark:text-rose-400 hover:underline flex items-center gap-1">
                    Lihat semua <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                @foreach($youtubeVideos as $video)
                <div class="group relative flex flex-col overflow-hidden rounded-2xl border border-slate-200/70 bg-white dark:border-slate-700/60 dark:bg-slate-800/50 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300">

                    <div class="relative overflow-hidden aspect-video bg-slate-200 dark:bg-slate-700">
                        <img
                            src="{{ $video->thumbnail_url }}"
                            alt="{{ $video->title }}"
                            class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                            loading="lazy"
                            onerror="this.src='https://i.ytimg.com/vi/{{ $video->video_id }}/mqdefault.jpg'"
                        >
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                        <a href="{{ $video->watch_url }}"
                           target="_blank" rel="noopener"
                           class="absolute inset-0 flex items-center justify-center"
                           aria-label="Tonton {{ $video->title }}">
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-rose-600/90 text-white shadow-lg shadow-rose-600/40 backdrop-blur-sm scale-90 group-hover:scale-100 transition-transform duration-300">
                                <i class="fa-solid fa-play text-base ml-0.5"></i>
                            </div>
                        </a>

                        <span class="absolute bottom-2 right-2 rounded-md bg-black/80 px-1.5 py-0.5 text-[10px] font-semibold text-white backdrop-blur-sm">
                            {{ $video->duration }}
                        </span>
                        <span class="absolute top-2 left-2 inline-flex items-center gap-1 rounded-lg bg-rose-600 px-2 py-1 text-[10px] font-bold text-white shadow-sm">
                            <i class="fa-brands fa-youtube text-[9px]"></i> YouTube
                        </span>
                    </div>

                    <div class="flex flex-col flex-grow p-4">
                        <h4 class="text-sm font-bold leading-snug text-slate-900 dark:text-white mb-1.5 line-clamp-2 group-hover:text-rose-600 dark:group-hover:text-rose-400 transition-colors">
                            <a href="{{ $video->watch_url }}" target="_blank" rel="noopener">
                                {{ $video->title }}
                            </a>
                        </h4>
                        <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-2 leading-relaxed flex-grow">
                            {{ $video->description }}
                        </p>
                        <div class="flex items-center gap-3 mt-3 pt-3 border-t border-slate-100 dark:border-slate-700/60">
                            <span class="text-[10px] text-slate-400 flex items-center gap-1">
                                <i class="fa-regular fa-eye"></i> {{ $video->views }} views
                            </span>
                            <a href="{{ $video->watch_url }}"
                               target="_blank" rel="noopener"
                               class="ml-auto inline-flex items-center gap-1 text-[11px] font-bold text-rose-600 dark:text-rose-400 hover:gap-2 transition-all">
                                Tonton <i class="fa-solid fa-arrow-right text-[9px]"></i>
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- ══════════════════════════════════════════════════════════ --}}
        {{-- TIKTOK SECTION                                            --}}
        {{-- ══════════════════════════════════════════════════════════ --}}
        @if($tiktokVideos->isNotEmpty())
        <div>
            <div class="flex items-center gap-3 mb-6">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-slate-900 dark:bg-white text-white dark:text-slate-900 shadow-md">
                    <i class="fa-brands fa-tiktok text-lg"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">TikTok — Tips & Tricks Cepat</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Short video padat isi, langsung ke intinya</p>
                </div>
                <a href="{{ $tiktokProfile }}" target="_blank" rel="noopener"
                   class="ml-auto text-xs font-semibold text-slate-700 dark:text-slate-300 hover:underline flex items-center gap-1">
                    Lihat semua <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-5">
                @foreach($tiktokVideos as $video)
                <div class="group relative flex flex-col overflow-hidden rounded-2xl border border-slate-200/70 bg-white dark:border-slate-700/60 dark:bg-slate-800/50 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300">

                    {{-- Portrait thumbnail (9:16 ratio) --}}
                    <div class="relative overflow-hidden bg-slate-900 dark:bg-slate-950" style="padding-bottom: 177.78%">

                        @if($video->thumbnail_url)
                            {{-- Real thumbnail fetched from TikTok oEmbed --}}
                            <img
                                src="{{ $video->thumbnail_url }}"
                                alt="{{ $video->title }}"
                                class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                                loading="lazy"
                                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                            >
                            {{-- Gradient fallback (hidden unless img fails) --}}
                            <div class="absolute inset-0 flex-col items-center justify-center bg-gradient-to-br from-slate-800 to-slate-950 hidden">
                                <div class="absolute inset-0 opacity-10">
                                    <div class="absolute top-4 left-4 h-32 w-32 rounded-full bg-cyan-400 blur-3xl"></div>
                                    <div class="absolute bottom-4 right-4 h-32 w-32 rounded-full bg-rose-500 blur-3xl"></div>
                                </div>
                                <i class="fa-brands fa-tiktok text-5xl text-white/20"></i>
                            </div>
                        @else
                            {{-- Gradient placeholder when no thumbnail stored yet --}}
                            <div class="absolute inset-0 flex flex-col items-center justify-center bg-gradient-to-br from-slate-800 to-slate-950">
                                <div class="absolute inset-0 opacity-10">
                                    <div class="absolute top-4 left-4 h-32 w-32 rounded-full bg-cyan-400 blur-3xl"></div>
                                    <div class="absolute bottom-4 right-4 h-32 w-32 rounded-full bg-rose-500 blur-3xl"></div>
                                </div>
                                <i class="fa-brands fa-tiktok text-5xl text-white/20"></i>
                            </div>
                        @endif

                        {{-- Dark hover overlay --}}
                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-colors duration-300 pointer-events-none"></div>

                        {{-- Clickable play overlay --}}
                        <a href="{{ $video->watch_url }}"
                           target="_blank" rel="noopener"
                           class="absolute inset-0 flex flex-col items-center justify-center"
                           aria-label="Tonton {{ $video->title }}">
                            <div class="flex h-14 w-14 items-center justify-center rounded-full bg-white/15 border-2 border-white/40 backdrop-blur-sm text-white scale-90 group-hover:scale-100 transition-transform duration-300 shadow-xl">
                                <i class="fa-solid fa-play text-xl ml-0.5"></i>
                            </div>
                        </a>

                        {{-- Duration badge --}}
                        <span class="absolute bottom-2 right-2 rounded-md bg-black/70 px-1.5 py-0.5 text-[10px] font-semibold text-white backdrop-blur-sm">
                            {{ $video->duration }}
                        </span>

                        {{-- TikTok badge --}}
                        <span class="absolute top-2 left-2 inline-flex items-center gap-1 rounded-lg bg-slate-900/90 px-2 py-1 text-[10px] font-bold text-white shadow-sm border border-white/10 backdrop-blur-sm">
                            <i class="fa-brands fa-tiktok text-[9px]"></i> TikTok
                        </span>

                        {{-- Views badge --}}
                        <span class="absolute top-2 right-2 rounded-lg bg-black/60 px-2 py-1 text-[10px] font-semibold text-white backdrop-blur-sm flex items-center gap-1">
                            <i class="fa-solid fa-heart text-rose-400 text-[9px]"></i> {{ $video->views }}
                        </span>
                    </div>

                    {{-- Card Content --}}
                    <div class="p-3.5">
                        <h4 class="text-xs font-bold leading-snug text-slate-900 dark:text-white mb-1 line-clamp-2 group-hover:text-slate-600 dark:group-hover:text-slate-300 transition-colors">
                            {{ $video->title }}
                        </h4>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 line-clamp-2 leading-relaxed">
                            {{ $video->description }}
                        </p>
                        <a href="{{ $video->watch_url }}"
                           target="_blank" rel="noopener"
                           class="mt-2.5 flex items-center justify-center gap-1.5 rounded-xl bg-slate-900 dark:bg-white py-2 text-[11px] font-bold text-white dark:text-slate-900 hover:bg-slate-700 dark:hover:bg-slate-100 transition-all">
                            <i class="fa-brands fa-tiktok text-[10px]"></i> Tonton di TikTok
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>
</section>
