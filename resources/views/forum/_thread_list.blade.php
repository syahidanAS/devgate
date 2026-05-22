@if(request('tag'))
<div class="mb-4 flex items-center gap-2" id="active-tag-indicator" data-active-tag="{{ request('tag') }}">
    <span class="text-sm text-slate-500 dark:text-slate-400">Filter tag:</span>
    <span class="inline-flex items-center gap-1.5 rounded-full bg-indigo-100 dark:bg-indigo-900/40 border border-indigo-200 dark:border-indigo-800 px-3 py-1 text-xs font-bold text-indigo-700 dark:text-indigo-300">
        <i class="fa-solid fa-tag text-[10px]"></i> {{ request('tag') }}
        <a href="{{ route('forum.index', array_filter(['filter' => request('filter'), 'search' => request('search')])) }}" 
           class="tag-reset-link ml-1 hover:text-red-500"
           data-tag-reset>×</a>
    </span>
</div>
@endif

{{-- Thread list --}}
@forelse($threads as $thread)
<a href="{{ route('forum.show', $thread->slug) }}"
   class="group block rounded-2xl border border-slate-200/70 dark:border-slate-800/70 bg-white/70 dark:bg-slate-900/70 backdrop-blur p-5 mb-3 hover:border-indigo-400/50 dark:hover:border-indigo-600/50 hover:shadow-lg hover:shadow-indigo-500/5 transition-all">
    <div class="flex items-start gap-4">

        {{-- Avatar --}}
        <img src="{{ $thread->user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($thread->user->name).'&background=6366f1&color=fff&size=80' }}"
             alt="{{ $thread->user->name }}"
             class="h-10 w-10 rounded-xl object-cover shrink-0 ring-1 ring-slate-200 dark:ring-slate-700">

        <div class="flex-1 min-w-0">
            <div class="flex items-start justify-between gap-2">
                <h2 class="font-bold text-slate-800 dark:text-slate-100 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors line-clamp-2 leading-snug">
                    {{ $thread->title }}
                </h2>
                @if($thread->is_solved)
                    <span class="shrink-0 inline-flex items-center gap-1 rounded-full bg-emerald-100 dark:bg-emerald-900/40 border border-emerald-200 dark:border-emerald-800 px-2 py-0.5 text-[10px] font-bold text-emerald-700 dark:text-emerald-400">
                        <i class="fa-solid fa-check"></i> Terjawab
                    </span>
                @endif
            </div>

            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400 line-clamp-2">{{ $thread->excerpt }}</p>

            <div class="mt-3 flex flex-wrap items-center gap-3 text-xs text-slate-400 dark:text-slate-500">
                {{-- Tags --}}
                @if($thread->tags)
                    <div class="flex flex-wrap gap-1">
                        @foreach($thread->tags as $tag)
                            <span class="inline-flex items-center rounded-md bg-slate-100 dark:bg-slate-800 px-2 py-0.5 text-[10px] font-semibold text-slate-600 dark:text-slate-400">{{ $tag }}</span>
                        @endforeach
                    </div>
                @endif
                <span class="flex items-center gap-1"><i class="fa-regular fa-comment"></i> {{ $thread->replies_count }}</span>
                <span class="flex items-center gap-1"><i class="fa-regular fa-eye"></i> {{ $thread->views }}</span>
                <span class="ml-auto">{{ $thread->user->name }} · {{ $thread->created_at->diffForHumans() }}</span>
            </div>
        </div>
    </div>
</a>
@empty
<div class="rounded-2xl border border-dashed border-slate-300 dark:border-slate-700 p-12 text-center">
    <i class="fa-solid fa-comments text-4xl text-slate-300 dark:text-slate-700 mb-3 block"></i>
    <p class="font-semibold text-slate-500 dark:text-slate-400">Belum ada topik</p>
    <p class="text-sm text-slate-400 dark:text-slate-500 mt-1">Jadilah yang pertama memulai diskusi!</p>
</div>
@endforelse

{{-- Pagination --}}
<div class="mt-6">{{ $threads->links() }}</div>
