@extends('layouts.app')

@section('title', 'DevGate Blog — IoT Specialist, Embedded & Web Dev Articles')

@section('content')
<div class="py-8 sm:py-12">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        
        <!-- Header Banner Section -->
        <div class="mb-10 text-center md:text-left flex flex-col md:flex-row items-center justify-between gap-6 border-b border-slate-200/50 pb-8 dark:border-slate-800/50">
            <div>
                <h1 class="text-3xl font-extrabold tracking-tight sm:text-4xl bg-gradient-to-r from-slate-900 via-indigo-950 to-indigo-600 bg-clip-text text-transparent dark:from-white dark:via-slate-200 dark:to-indigo-400">
                    Blog & Wawasan Teknologi
                </h1>
                <p class="mt-2 text-sm sm:text-base text-slate-500 dark:text-slate-400">
                    Jelajahi artikel mendalam tentang IoT Specialist, Embedded System, Automation, AI, dan Web Development.
                </p>
            </div>
            
            <!-- Search bar form -->
            <form action="{{ route('blog.index') }}" method="GET" class="relative max-w-sm w-full">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari artikel..." class="w-full rounded-2xl border border-slate-200 bg-white/70 pl-11 pr-4 py-2.5 text-sm dark:border-slate-800 dark:bg-slate-900/70 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 transition-all backdrop-blur-sm">
                <i class="fa-solid fa-magnifying-glass absolute left-4 top-3.5 text-slate-400 text-sm"></i>
            </form>
        </div>

        @if(!request('search') && !request('category') && !request('tag') && $featuredArticles->count() > 0)
            <!-- Featured Articles Grid (Wow visual) -->
            <div class="mb-14">
                <h2 class="text-xs font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400 mb-6 flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-indigo-600 dark:bg-indigo-400 animate-ping"></span>
                    Artikel Pilihan Utama
                </h2>
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Main Large Featured Card -->
                    @php $mainFeatured = $featuredArticles->first(); @endphp
                    <div class="lg:col-span-2 relative group rounded-3xl overflow-hidden border border-slate-200/40 bg-slate-900/50 dark:border-slate-800/40 shadow-xl h-[420px]">
                        <img src="{{ $mainFeatured->thumbnail_url }}" alt="{{ $mainFeatured->title }}" class="absolute inset-0 h-full w-full object-cover transition-transform duration-700 group-hover:scale-105 opacity-80 lg:opacity-90">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/40 to-transparent"></div>
                        
                        <div class="absolute bottom-0 left-0 right-0 p-6 sm:p-8 flex flex-col justify-end text-white">
                            <div class="flex items-center gap-3 text-xs mb-3">
                                <span class="rounded-lg bg-indigo-600 px-2.5 py-1 font-bold uppercase text-[10px] tracking-wider text-white">
                                    {{ $mainFeatured->category->name }}
                                </span>
                                <span class="text-slate-300 font-medium">
                                    <i class="fa-regular fa-clock mr-1"></i> {{ $mainFeatured->reading_time_text }}
                                </span>
                            </div>
                            <h3 class="text-xl sm:text-3xl font-bold tracking-tight mb-3 group-hover:text-indigo-400 transition-colors">
                                <a href="{{ route('blog.show', $mainFeatured->slug) }}">{{ $mainFeatured->title }}</a>
                            </h3>
                            <p class="text-slate-300 text-sm line-clamp-2 max-w-2xl mb-4 font-light">
                                {{ $mainFeatured->excerpt }}
                            </p>
                            <div class="flex items-center gap-3">
                                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-800 border border-slate-700 text-xs font-semibold">
                                    {{ strtoupper(substr($mainFeatured->author->name, 0, 2)) }}
                                </div>
                                <span class="text-xs font-medium text-slate-200">{{ $mainFeatured->author->name }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Side Featured Cards Stack -->
                    <div class="flex flex-col gap-6 justify-between">
                        @foreach($featuredArticles->skip(1) as $sideFeatured)
                            <div class="relative group rounded-3xl overflow-hidden border border-slate-200/40 bg-slate-900/50 dark:border-slate-800/40 shadow-lg h-[198px] flex flex-col justify-end">
                                <img src="{{ $sideFeatured->thumbnail_url }}" alt="{{ $sideFeatured->title }}" class="absolute inset-0 h-full w-full object-cover transition-transform duration-700 group-hover:scale-105 opacity-80">
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/60 to-transparent"></div>
                                
                                <div class="absolute bottom-0 left-0 right-0 p-5 text-white">
                                    <div class="flex items-center gap-3 text-[10px] mb-2">
                                        <span class="rounded bg-indigo-600/90 px-2 py-0.5 font-bold uppercase tracking-wider text-white">
                                            {{ $sideFeatured->category->name }}
                                        </span>
                                        <span class="text-slate-300 font-medium">
                                            <i class="fa-regular fa-clock mr-1"></i> {{ $sideFeatured->reading_time_text }}
                                        </span>
                                    </div>
                                    <h4 class="text-base sm:text-lg font-bold leading-snug line-clamp-2 mb-2 group-hover:text-indigo-400 transition-colors">
                                        <a href="{{ route('blog.show', $sideFeatured->slug) }}">{{ $sideFeatured->title }}</a>
                                    </h4>
                                    <span class="text-xs font-medium text-slate-300">{{ $sideFeatured->author->name }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        {{-- ── Video Tutorial Section (only on main blog page, not search/filter) ── --}}
        @if(!request('search') && !request('category') && !request('tag'))
            </div>{{-- close max-w-7xl --}}
            @include('blog.partials.video-tutorials')
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        @endif

        {{-- Main Content Area Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            
            <!-- Articles Feed (Col span 3) -->
            <div class="lg:col-span-3">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold tracking-tight text-slate-900 dark:text-white">
                        @if(request('search'))
                            Hasil Pencarian: "{{ request('search') }}"
                        @elseif(request('category'))
                            Kategori: "{{ request('category') }}"
                        @elseif(request('tag'))
                            Tag: "#{{ request('tag') }}"
                        @else
                            Daftar Artikel Terbaru
                        @endif
                    </h2>
                    <span class="text-xs text-slate-500 dark:text-slate-400">Total {{ $articles->total() }} artikel</span>
                </div>

                @if($articles->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($articles as $article)
                            <article class="flex flex-col justify-between overflow-hidden rounded-3xl border border-slate-200/60 bg-white/70 p-5 shadow-sm hover:shadow-md dark:border-slate-800/80 dark:bg-slate-900/40 hover:scale-[1.01] transition-all backdrop-blur-sm">
                                <div>
                                    <!-- Card Image Area -->
                                    <div class="relative w-full h-44 rounded-2xl overflow-hidden mb-4 bg-slate-100 dark:bg-slate-800">
                                        <img src="{{ $article->thumbnail_url }}" alt="{{ $article->title }}" class="h-full w-full object-cover" loading="lazy">
                                        <span class="absolute top-3 left-3 rounded-lg bg-white/95 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-slate-800 dark:bg-slate-900/95 dark:text-indigo-400 shadow-sm border border-slate-100 dark:border-slate-800">
                                            {{ $article->category->name }}
                                        </span>
                                    </div>
                                    
                                    <!-- Meta Info -->
                                    <div class="flex items-center gap-3 text-xs text-slate-400 dark:text-slate-500 mb-2">
                                        <span>{{ $article->published_at?->format('d M Y') }}</span>
                                        <span>&bull;</span>
                                        <span><i class="fa-regular fa-clock mr-1"></i> {{ $article->reading_time_text }}</span>
                                    </div>
                                    
                                    <!-- Title & Excerpt -->
                                    <h3 class="text-lg font-bold tracking-tight text-slate-900 dark:text-white leading-snug mb-2 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                                        <a href="{{ route('blog.show', $article->slug) }}">{{ $article->title }}</a>
                                    </h3>
                                    <p class="text-sm text-slate-500 dark:text-slate-400 line-clamp-3 mb-4 leading-relaxed font-light">
                                        {{ $article->excerpt }}
                                    </p>
                                </div>
                                
                                <!-- Card Footer: Author Info -->
                                <div class="flex items-center justify-between border-t border-slate-100 dark:border-slate-800 pt-4 mt-auto">
                                    <div class="flex items-center gap-2.5">
                                        <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-indigo-600 text-[10px] font-semibold text-white">
                                            {{ strtoupper(substr($article->author->name, 0, 2)) }}
                                        </div>
                                        <span class="text-xs font-medium text-slate-700 dark:text-slate-300">{{ $article->author->name }}</span>
                                    </div>
                                    <a href="{{ route('blog.show', $article->slug) }}" class="inline-flex items-center gap-1 text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:gap-2 transition-all">
                                        Baca <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                    </a>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    <!-- Custom Pagination Wrapper -->
                    <div class="mt-10">
                        {{ $articles->links() }}
                    </div>
                @else
                    <!-- Empty Articles State -->
                    <div class="flex flex-col items-center justify-center py-16 text-center rounded-3xl border border-dashed border-slate-200 dark:border-slate-800 bg-white/30 dark:bg-slate-900/10">
                        <i class="fa-solid fa-folder-open text-slate-300 dark:text-slate-700 text-5xl mb-4"></i>
                        <h3 class="text-lg font-bold text-slate-700 dark:text-slate-300">Tidak ada artikel</h3>
                        <p class="text-sm text-slate-400 mt-1 max-w-sm">Maaf, kami tidak menemukan artikel yang cocok dengan filter atau kata kunci Anda.</p>
                        <a href="{{ route('blog.index') }}" class="mt-4 inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2 text-xs font-semibold text-white shadow-md hover:bg-indigo-500 transition-colors">
                            Reset Filter
                        </a>
                    </div>
                @endif
            </div>

            <!-- Sidebar Filters Area (Col span 1) -->
            <div class="lg:col-span-1 flex flex-col gap-8">
                
                <!-- Category widget -->
                <div class="rounded-3xl border border-slate-200/60 bg-white/70 p-6 shadow-sm dark:border-slate-800/80 dark:bg-slate-900/40 backdrop-blur-sm">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-layer-group text-indigo-500"></i> Kategori
                    </h3>
                    <div class="flex flex-col gap-2">
                        @foreach($categories as $category)
                            <a href="{{ route('blog.index', ['category' => $category->slug]) }}" class="flex items-center justify-between rounded-xl px-3.5 py-2 text-sm text-slate-600 hover:bg-slate-50 dark:text-slate-300 dark:hover:bg-slate-800 transition-all border border-transparent hover:border-slate-200/40 dark:hover:border-slate-700/40 {{ request('category') === $category->slug ? 'bg-indigo-50 border-indigo-100 text-indigo-700 dark:bg-indigo-950/40 dark:border-indigo-900/40 dark:text-indigo-400 font-semibold' : '' }}">
                                <span class="flex items-center gap-2">
                                    <span class="h-2 w-2 rounded-full" style="background-color: {{ $category->color ?? '#6366f1' }}"></span>
                                    {{ $category->name }}
                                </span>
                                <span class="text-xs px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400">{{ $category->articles_count ?? $category->articles()->published()->count() }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>

                <!-- Trending Sidebar Panel -->
                @if($trendingArticles->count() > 0)
                    <div class="rounded-3xl border border-slate-200/60 bg-white/70 p-6 shadow-sm dark:border-slate-800/80 dark:bg-slate-900/40 backdrop-blur-sm">
                        <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-chart-line text-indigo-500"></i> Terpopuler
                        </h3>
                        <div class="flex flex-col gap-4">
                            @foreach($trendingArticles as $index => $trending)
                                <a href="{{ route('blog.show', $trending->slug) }}" class="flex items-start gap-3 group">
                                    <span class="text-2xl font-extrabold text-slate-200 dark:text-slate-800 leading-none group-hover:text-indigo-500 transition-colors w-6">
                                        {{ sprintf("%02d", $index + 1) }}
                                    </span>
                                    <div class="flex-grow">
                                        <h4 class="text-sm font-semibold leading-snug text-slate-700 dark:text-slate-300 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors line-clamp-2">
                                            {{ $trending->title }}
                                        </h4>
                                        <span class="text-[10px] text-slate-400 mt-1 block"><i class="fa-regular fa-eye mr-1"></i> {{ number_format($trending->view_count) }} views</span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Tags cloud widget -->
                @if($tags->count() > 0)
                    <div class="rounded-3xl border border-slate-200/60 bg-white/70 p-6 shadow-sm dark:border-slate-800/80 dark:bg-slate-900/40 backdrop-blur-sm">
                        <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-tags text-indigo-500"></i> Tag Populer
                        </h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($tags as $tag)
                                <a href="{{ route('blog.index', ['tag' => $tag->slug]) }}" class="text-xs px-3 py-1.5 rounded-xl border border-slate-200/80 bg-slate-50 text-slate-600 hover:bg-slate-100 hover:text-indigo-600 dark:border-slate-800 dark:bg-slate-900/40 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-indigo-400 transition-all {{ request('tag') === $tag->slug ? 'bg-indigo-50 border-indigo-200 text-indigo-600 dark:bg-indigo-950/60 dark:border-indigo-900 dark:text-indigo-400 font-semibold' : '' }}">
                                    #{{ $tag->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>

        </div>

    </div>
</div>
@endsection
