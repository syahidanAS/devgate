@extends('layouts.app')

@section('title', 'Kategori: ' . $category->name . ' — DevGate Blog')

@section('content')
<div class="py-8 sm:py-12">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        
        <!-- Category Landing Card -->
        <div class="relative overflow-hidden rounded-3xl border border-slate-200/50 bg-white/70 p-6 sm:p-10 shadow-sm dark:border-slate-800/50 dark:bg-slate-900/40 mb-10 backdrop-blur-sm">
            <div class="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-indigo-600/10 blur-3xl pointer-events-none"></div>
            
            <div class="flex items-center gap-3 mb-2">
                <span class="h-3.5 w-3.5 rounded-full" style="background-color: {{ $category->color ?? '#6366f1' }}"></span>
                <span class="text-xs font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400">Kategori Blog</span>
            </div>
            
            <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white sm:text-4xl">
                {{ $category->name }}
            </h1>
            
            @if($category->description)
                <p class="mt-3 text-sm sm:text-base text-slate-500 dark:text-slate-400 max-w-2xl leading-relaxed">
                    {{ $category->description }}
                </p>
            @endif
        </div>

        <!-- Articles Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($articles as $article)
                <article class="flex flex-col justify-between overflow-hidden rounded-3xl border border-slate-200/60 bg-white/70 p-5 shadow-sm hover:shadow-md dark:border-slate-800/80 dark:bg-slate-900/40 hover:scale-[1.01] transition-all backdrop-blur-sm">
                    <div>
                        <!-- Card Image Area -->
                        <div class="relative w-full h-44 rounded-2xl overflow-hidden mb-4 bg-slate-100 dark:bg-slate-800">
                            <img src="{{ $article->thumbnail_url }}" alt="{{ $article->title }}" class="h-full w-full object-cover" loading="lazy">
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
            @empty
                <div class="col-span-full flex flex-col items-center justify-center py-16 text-center rounded-3xl border border-dashed border-slate-200 dark:border-slate-800 bg-white/30 dark:bg-slate-900/10">
                    <i class="fa-solid fa-folder-open text-slate-300 dark:text-slate-700 text-5xl mb-4"></i>
                    <h3 class="text-lg font-bold text-slate-700 dark:text-slate-300">Tidak ada artikel</h3>
                    <p class="text-sm text-slate-400 mt-1 max-w-sm">Maaf, kami tidak menemukan artikel di bawah kategori ini saat ini.</p>
                    <a href="{{ route('blog.index') }}" class="mt-4 inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2 text-xs font-semibold text-white shadow-md hover:bg-indigo-500 transition-colors">
                        Kembali ke Blog
                    </a>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-10">
            {{ $articles->links() }}
        </div>

    </div>
</div>
@endsection
