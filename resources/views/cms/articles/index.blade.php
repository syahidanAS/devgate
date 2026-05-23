@extends('layouts.cms')

@section('title', 'Kelola Artikel — DevGate')

@section('breadcrumbs')
<div class="flex items-center gap-2 text-xs font-semibold text-slate-400">
    <span>Portal CMS</span>
    <i class="fa-solid fa-chevron-right text-[8px]"></i>
    <span>Blog & Artikel</span>
    <i class="fa-solid fa-chevron-right text-[8px]"></i>
    <span class="text-white">Daftar</span>
</div>
@endsection

@section('content')
<div class="space-y-6">

    <!-- Page Header & Action Controls -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between border-b border-slate-800/60 pb-6 gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight flex items-center gap-3">
                <i class="fa-solid fa-pen-nib text-indigo-500"></i> Kelola Artikel
            </h1>
            <p class="text-slate-400 text-xs mt-1">Buat, publikasikan, jadwalkan, atau edit artikel teknologi dan IoT di DevGate.</p>
        </div>

        <div class="flex items-center gap-3 self-end md:self-auto">
            <a 
                href="{{ route('cms.articles.create') }}" 
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-indigo-600 hover:bg-indigo-500 text-xs font-bold text-white shadow-lg shadow-indigo-600/25 transition-all"
            >
                <i class="fa-solid fa-plus text-[10px]"></i> Tulis Artikel Baru
            </a>
        </div>
    </div>

    <!-- Articles Table Card -->
    <div class="rounded-3xl border border-slate-800/80 bg-slate-950/45 overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm border-collapse">
                <thead>
                    <tr class="text-[10px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-800/60 bg-slate-950/60">
                        <th class="py-4 px-6">Artikel</th>
                        <th class="py-4 px-6">Kategori</th>
                        <th class="py-4 px-6">Penulis</th>
                        <th class="py-4 px-6">Status</th>
                        <th class="py-4 px-6">Statistik</th>
                        <th class="py-4 px-6 text-center">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-850">
                    @forelse($articles as $article)
                        <tr class="group hover:bg-slate-900/20 transition-all">
                            <!-- Title & Thumbnail -->
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-4 max-w-md">
                                    <div class="h-12 w-20 rounded-xl overflow-hidden bg-slate-900 border border-slate-800 shrink-0 flex items-center justify-center">
                                        @if($article->thumbnail)
                                            <img src="{{ $article->thumbnail_url }}" alt="{{ $article->title }}" class="h-full w-full object-cover">
                                        @else
                                            <div class="text-slate-700 text-lg">
                                                <i class="fa-solid fa-image"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="overflow-hidden">
                                        <h3 class="text-xs sm:text-sm font-bold text-white group-hover:text-indigo-400 transition-colors leading-snug truncate" title="{{ $article->title }}">
                                            {{ $article->title }}
                                        </h3>
                                        <p class="text-[10px] text-slate-500 font-mono mt-1">
                                            slug: {{ $article->slug }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            <!-- Category -->
                            <td class="py-4 px-6">
                                @if($article->category)
                                    <span 
                                        class="inline-flex items-center rounded-lg border px-2 py-0.5 text-[9px] font-bold uppercase tracking-wider"
                                        style="background-color: {{ $article->category->color }}15; border-color: {{ $article->category->color }}30; color: {{ $article->category->color }};"
                                    >
                                        {{ $article->category->name }}
                                    </span>
                                @else
                                    <span class="text-xs text-slate-650 font-semibold">-</span>
                                @endif
                            </td>

                            <!-- Author -->
                            <td class="py-4 px-6 text-slate-300">
                                <div class="text-xs font-semibold">
                                    {{ $article->author->name ?? 'Sistem' }}
                                </div>
                                <div class="text-[9px] text-slate-500 font-mono mt-0.5">
                                    &#64;{{ $article->author->username ?? 'admin' }}
                                </div>
                            </td>

                            <!-- Status -->
                            <td class="py-4 px-6">
                                @if($article->status === 'published')
                                    <span class="inline-flex items-center rounded-lg border border-emerald-500/20 bg-emerald-500/10 px-2 py-0.5 text-[9px] font-bold uppercase tracking-wider text-emerald-400">
                                        <span class="h-1 w-1 rounded-full bg-emerald-500 mr-1.5 animate-pulse"></span> Published
                                    </span>
                                    <div class="text-[9px] text-slate-500 mt-1">
                                        {{ $article->published_at ? $article->published_at->format('d M Y, H:i') : '' }}
                                    </div>
                                @elseif($article->status === 'scheduled')
                                    <span class="inline-flex items-center rounded-lg border border-amber-500/20 bg-amber-500/10 px-2 py-0.5 text-[9px] font-bold uppercase tracking-wider text-amber-400">
                                        <span class="h-1 w-1 rounded-full bg-amber-500 mr-1.5"></span> Scheduled
                                    </span>
                                    <div class="text-[9px] text-slate-500 mt-1">
                                        Rilis: {{ $article->scheduled_at ? $article->scheduled_at->format('d M Y, H:i') : '' }}
                                    </div>
                                @else
                                    <span class="inline-flex items-center rounded-lg border border-slate-700 bg-slate-800/40 px-2 py-0.5 text-[9px] font-bold uppercase tracking-wider text-slate-400">
                                        Draft
                                    </span>
                                    <div class="text-[9px] text-slate-500 mt-1">
                                        Terakhir diubah: {{ $article->updated_at->format('d M Y') }}
                                    </div>
                                @endif
                            </td>

                            <!-- Statistics -->
                            <td class="py-4 px-6 text-slate-350 font-mono text-xs">
                                <div class="flex items-center gap-1.5">
                                    <i class="fa-regular fa-eye text-slate-500 text-[10px]"></i>
                                    <span>{{ number_format($article->view_count ?? 0) }} x</span>
                                </div>
                                @if($article->is_featured)
                                    <div class="mt-1 flex items-center">
                                        <span class="inline-flex items-center gap-1 text-[8px] font-bold uppercase tracking-wider px-1.5 py-0.5 rounded bg-indigo-500/10 border border-indigo-500/25 text-indigo-400">
                                            <i class="fa-solid fa-star text-[7px]"></i> Featured
                                        </span>
                                    </div>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="py-4 px-6">
                                <div class="flex items-center justify-center gap-2">
                                    <a 
                                        href="{{ route('cms.articles.edit', $article->id) }}" 
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-800 bg-slate-900 text-xs font-bold text-slate-355 hover:bg-indigo-600 hover:border-indigo-500 hover:text-white transition-all shadow-sm"
                                    >
                                        <i class="fa-solid fa-pen-to-square text-[10px]"></i> Edit
                                    </a>

                                    <form action="{{ route('cms.articles.destroy', $article->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus artikel ini?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button 
                                            type="submit" 
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-800 bg-slate-900 text-xs font-bold text-rose-400 hover:bg-rose-600 hover:border-rose-500 hover:text-white transition-all shadow-sm"
                                        >
                                            <i class="fa-solid fa-trash-can text-[10px]"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-16 text-center text-slate-500">
                                <i class="fa-solid fa-receipt text-5xl text-slate-700 mb-4 block animate-bounce"></i>
                                <span class="text-sm font-bold block text-slate-400 mb-1">Belum Ada Artikel</span>
                                <span class="text-xs text-slate-600">Klik tombol "Tulis Artikel Baru" di atas untuk mulai menulis konten pertama Anda.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($articles->hasPages())
            <div class="px-6 py-4 border-t border-slate-850 bg-slate-950/20">
                {{ $articles->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
