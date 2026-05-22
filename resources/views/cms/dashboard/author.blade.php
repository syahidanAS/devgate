@extends('layouts.cms')

@section('title', 'Author Dashboard — DevGate')

@section('breadcrumbs')
<div class="flex items-center gap-2 text-xs font-semibold text-slate-400">
    <span>Portal CMS</span>
    <i class="fa-solid fa-chevron-right text-[8px]"></i>
    <span class="text-white">Author Dashboard</span>
</div>
@endsection

@section('content')
<div class="space-y-8">
    
    <!-- Hero Banner Welcome -->
    <div class="relative overflow-hidden rounded-3xl border border-indigo-500/20 bg-gradient-to-r from-slate-950 via-slate-900/60 to-indigo-950/40 p-6 sm:p-8 shadow-xl">
        <div class="absolute right-0 top-0 h-full w-1/3 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-indigo-500/10 via-transparent to-transparent pointer-events-none"></div>
        <div class="relative z-10 max-w-xl">
            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-500/10 px-3 py-1 text-xs font-bold text-emerald-450 border border-emerald-500/20 mb-4">
                <i class="fa-solid fa-feather text-[10px]"></i> Penulis Konten Aktif
            </span>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white leading-tight">
                Ruang Kreatif Penulis,<br>
                <span class="bg-gradient-to-r from-indigo-400 to-cyan-400 bg-clip-text text-transparent">{{ Auth::user()->name }}</span>
            </h1>
            <p class="mt-2 text-slate-400 text-sm leading-relaxed">
                Tulis artikel berkualitas mengenai teknologi IoT, sistem tertanam, otomatisasi, web development, atau AI. Pantau performa tayangan artikel dan kelola draf tulisan Anda dengan mudah.
            </p>
        </div>
    </div>

    <!-- Stats Summary Cards (Four Grid) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <!-- My Articles -->
        <div class="relative group rounded-3xl border border-slate-800/80 bg-slate-950/45 p-6 transition-all hover:border-indigo-500/40 hover:bg-slate-950/60 shadow-lg">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Artikel Saya</span>
                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 group-hover:scale-105 transition-all">
                    <i class="fa-solid fa-newspaper text-lg"></i>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-3xl font-extrabold text-white tracking-tight">{{ number_format($stats['my_articles']) }}</h3>
                <span class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider mt-1 block">Total Konten</span>
            </div>
        </div>

        <!-- Published Articles -->
        <div class="relative group rounded-3xl border border-slate-800/80 bg-slate-950/45 p-6 transition-all hover:border-emerald-500/40 hover:bg-slate-950/60 shadow-lg">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Diterbitkan</span>
                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 group-hover:scale-105 transition-all">
                    <i class="fa-solid fa-circle-check text-lg"></i>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-3xl font-extrabold text-emerald-400 tracking-tight">{{ number_format($stats['published_articles']) }}</h3>
                <span class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider mt-1 block">Aktif Dibaca</span>
            </div>
        </div>

        <!-- Draft Articles -->
        <div class="relative group rounded-3xl border border-slate-800/80 bg-slate-950/45 p-6 transition-all hover:border-amber-500/40 hover:bg-slate-950/60 shadow-lg">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Draf Tulisan</span>
                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-amber-500/10 border border-amber-500/20 text-amber-400 group-hover:scale-105 transition-all">
                    <i class="fa-solid fa-folder-open text-lg"></i>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-3xl font-extrabold text-white tracking-tight">{{ number_format($stats['draft_articles']) }}</h3>
                <span class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider mt-1 block">Belum Terbit</span>
            </div>
        </div>

        <!-- Total Views -->
        <div class="relative group rounded-3xl border border-slate-800/80 bg-slate-950/45 p-6 transition-all hover:border-cyan-500/40 hover:bg-slate-950/60 shadow-lg">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Pembaca</span>
                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 group-hover:scale-105 transition-all">
                    <i class="fa-solid fa-eye text-lg"></i>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-3xl font-extrabold text-cyan-450 tracking-tight">{{ number_format($stats['total_views']) }}</h3>
                <span class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider mt-1 block">Total Tayangan</span>
            </div>
        </div>

    </div>

    <!-- Split Section: Recent Articles and Quick Action Panel -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Author's Recent Articles list (Col span 2) -->
        <div class="lg:col-span-2 rounded-3xl border border-slate-800/80 bg-slate-950/45 p-6 shadow-xl flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between border-b border-slate-800/60 pb-5 mb-5">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        <i class="fa-solid fa-newspaper text-indigo-500"></i> Tulisan Terbaru Saya
                    </h3>
                    <a href="{{ route('cms.articles.index') }}" class="text-xs font-semibold text-indigo-400 hover:text-indigo-300 transition-colors">Kelola Artikel</a>
                </div>

                <div class="space-y-4">
                    @forelse($myRecentArticles as $article)
                        <div class="flex items-center justify-between p-3.5 rounded-2xl border border-slate-850 bg-slate-900/30 hover:border-slate-800 hover:bg-slate-900/60 transition-all group">
                            <div class="flex items-center gap-4 overflow-hidden">
                                <div class="relative h-12 w-12 shrink-0 rounded-xl overflow-hidden bg-slate-800">
                                    <img src="{{ $article->thumbnail_url }}" alt="{{ $article->title }}" class="h-full w-full object-cover">
                                </div>
                                <div class="overflow-hidden">
                                    <h4 class="text-sm font-bold text-white truncate group-hover:text-indigo-400 transition-colors leading-tight">
                                        {{ $article->title }}
                                    </h4>
                                    <div class="flex items-center gap-3 mt-1.5 text-[10px] text-slate-500">
                                        <span><i class="fa-regular fa-calendar mr-1"></i> {{ $article->created_at->format('d M Y') }}</span>
                                        <span>&bull;</span>
                                        <span class="px-1.5 py-0.5 rounded bg-slate-800 text-indigo-355 text-[8px] font-bold uppercase tracking-wider">{{ $article->category->name }}</span>
                                        <span>&bull;</span>
                                        <span><i class="fa-regular fa-eye mr-1"></i> {{ number_format($article->view_count) }} pembaca</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-1 shrink-0">
                                <a href="{{ route('cms.articles.edit', $article->id) }}" class="p-2 text-slate-400 hover:text-indigo-400 rounded-lg hover:bg-slate-850 transition-all">
                                    <i class="fa-solid fa-pen text-xs"></i>
                                </a>
                                <a href="{{ route('blog.show', $article->slug) }}" target="_blank" class="p-2 text-slate-400 hover:text-cyan-400 rounded-lg hover:bg-slate-850 transition-all">
                                    <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="py-12 text-center text-slate-500 text-xs">
                            <i class="fa-solid fa-feather text-3xl text-slate-700 mb-3 block"></i>
                            Belum ada artikel yang Anda buat. Mulai menulis sekarang!
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Quick writer action panel (Col span 1) -->
        <div class="lg:col-span-1 rounded-3xl border border-slate-800/80 bg-slate-950/45 p-6 shadow-xl flex flex-col justify-between">
            <div class="space-y-6">
                <div>
                    <h3 class="text-lg font-bold text-white mb-2">Tindakan Cepat</h3>
                    <p class="text-slate-400 text-xs leading-relaxed">Pintasan praktis untuk mempermudah alur kerja penulisan konten Anda.</p>
                </div>

                <div class="flex flex-col gap-3">
                    <a href="{{ route('cms.articles.create') }}" class="flex items-center justify-between p-4 rounded-2xl border border-indigo-500/20 bg-indigo-500/5 hover:bg-indigo-500/10 hover:border-indigo-500/40 text-sm font-bold text-indigo-300 transition-all group">
                        <span class="flex items-center gap-3">
                            <i class="fa-solid fa-plus-circle text-base"></i> Tulis Artikel Baru
                        </span>
                        <i class="fa-solid fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
                    </a>

                    <a href="{{ route('blog.index') }}" target="_blank" class="flex items-center justify-between p-4 rounded-2xl border border-slate-800 bg-slate-900/40 hover:bg-slate-900/80 text-sm font-semibold text-slate-300 transition-all group">
                        <span class="flex items-center gap-3">
                            <i class="fa-solid fa-book-open text-base text-cyan-400"></i> Kunjungi Blog Publik
                        </span>
                        <i class="fa-solid fa-chevron-right text-[10px]"></i>
                    </a>
                </div>

                <div class="bg-slate-900/40 border border-slate-850 p-4 rounded-2xl space-y-2">
                    <h4 class="text-xs font-bold text-white flex items-center gap-2">
                        <i class="fa-solid fa-circle-info text-cyan-400"></i> Ketentuan Penulisan
                    </h4>
                    <ul class="text-[10px] text-slate-400 list-disc list-inside space-y-1.5 leading-relaxed">
                        <li>Gunakan heading terstruktur H2 & H3 untuk Table of Contents otomatis.</li>
                        <li>Format penulisan kode terotomatisasi dengan highlight.js.</li>
                        <li>Unggahan gambar otomatis dikompres ke format WebP hemat bandwidth.</li>
                    </ul>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection
