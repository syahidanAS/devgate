{{--
    Recursive reply card partial.
    Variables: $reply, $thread, $likedReplyIds, $depth (0 = top-level)
--}}
@php
    $isSolution = $reply->is_solution;
    $isLiked    = Auth::check() && $likedReplyIds->contains($reply->id);
    $hasChildren = $reply->children->isNotEmpty();
    $isNested   = $depth > 0;
    $snippetText = \Illuminate\Support\Str::limit(strip_tags($reply->body), 70);
@endphp

<div id="reply-{{ $reply->id }}" class="rounded-2xl border transition-all duration-300 relative overflow-hidden
     {{ $isSolution 
        ? 'border-emerald-500 shadow-md shadow-emerald-500/5 bg-gradient-to-r from-emerald-500/5 to-teal-500/5 dark:from-emerald-950/20 dark:to-teal-950/10' 
        : ($isNested 
            ? 'border-slate-200/40 dark:border-slate-700/50 bg-slate-50/50 dark:bg-slate-800/20'
            : 'border-slate-200/60 dark:border-slate-800 bg-white/60 dark:bg-slate-900/40') }}">
    
    {{-- Solution Ribbon --}}
    @if($isSolution)
        <div class="absolute top-0 right-0 bg-emerald-500 text-white text-[10px] font-extrabold uppercase px-3 py-1 rounded-bl-xl flex items-center gap-1">
            <i class="fa-solid fa-check-double"></i> Jawaban Terbaik
        </div>
    @endif

    <div class="{{ $isNested ? 'p-4 sm:p-5' : 'p-5 sm:p-6' }}">
        
        {{-- Reply Meta --}}
        <div class="flex items-center justify-between gap-3 mb-4">
            <div class="flex items-center gap-2.5">
                <img src="{{ $reply->user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($reply->user->name).'&background=6366f1&color=fff&size=80' }}"
                     alt="{{ $reply->user->name }}"
                     class="{{ $isNested ? 'h-8 w-8' : 'h-9 w-9' }} rounded-xl object-cover ring-1 ring-slate-100 dark:ring-slate-800">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-bold text-slate-800 dark:text-slate-200 block">{{ $reply->user->name }}</span>
                        @if($reply->user_id === $thread->user_id)
                            <span class="text-[9px] font-extrabold bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20 px-1.5 py-0.5 rounded-md">Pembuat</span>
                        @endif
                        @if($isNested)
                            <span class="text-[9px] font-semibold bg-violet-500/10 text-violet-600 dark:text-violet-400 border border-violet-500/20 px-1.5 py-0.5 rounded-md flex items-center gap-1">
                                <i class="fa-solid fa-reply text-[8px]"></i> Membalas
                            </span>
                        @endif
                    </div>
                    <span class="text-[10px] text-slate-400 dark:text-slate-500 block">{{ $reply->created_at->diffForHumans() }}</span>
                </div>
            </div>
            
            {{-- Actions: Solved / Delete (Right aligned) --}}
            <div class="flex items-center gap-2">
                
                {{-- Solution Handler (Only owner of thread, only on top-level) --}}
                @auth
                    @if(!$isNested && Auth::id() === $thread->user_id && !$isSolution)
                        <form action="{{ route('forum.solved', [$thread->id, $reply->id]) }}" method="POST" onsubmit="return confirm('Tandai balasan ini sebagai jawaban terbaik?')">
                            @csrf
                            <button type="submit"
                                    title="Tandai sebagai jawaban terbaik"
                                    class="inline-flex items-center gap-1 rounded-lg border border-emerald-500/30 bg-emerald-500/10 px-2.5 py-1 text-[10px] font-bold text-emerald-600 dark:text-emerald-400 hover:bg-emerald-500 hover:text-white transition-all">
                                <i class="fa-solid fa-circle-check"></i> Tandai Solusi
                            </button>
                        </form>
                    @endif

                    {{-- Delete Reply Handler (Owner of reply or superadmin) --}}
                    @if(Auth::id() === $reply->user_id || Auth::user()->hasRole('superadmin'))
                        <form action="{{ route('forum.replies.destroy', $reply->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus balasan ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    title="Hapus balasan"
                                    class="h-7 w-7 flex items-center justify-center rounded-lg border border-slate-200 dark:border-slate-800 text-slate-400 hover:text-rose-500 dark:hover:text-rose-400 hover:bg-rose-500/5 transition-all">
                                <i class="fa-solid fa-trash-can text-xs"></i>
                            </button>
                        </form>
                    @endif
                @endauth

            </div>
        </div>

        {{-- Reply Body --}}
        <div class="forum-prose text-slate-700 dark:text-slate-300 leading-relaxed {{ $isNested ? 'text-[13px]' : 'text-sm' }} break-words pl-1">
            {!! $reply->body !!}
        </div>

        {{-- Like / Actions footer --}}
        <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800/80 flex items-center gap-4">
            <button type="button" 
                    onclick="toggleLike({{ $reply->id }}, this)"
                    class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-xl border transition-all active:scale-95 duration-150
                        {{ $isLiked 
                            ? 'bg-rose-500/10 text-rose-500 border-rose-500/30' 
                            : 'border-slate-200 dark:border-slate-800 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300' }}">
                <i class="fa-solid fa-heart {{ $isLiked ? 'scale-110' : '' }}"></i>
                <span class="like-count font-bold">{{ $reply->likes }}</span> menyukai
            </button>

            {{-- Reply Button: triggers replyTo() to set parent_reply_id --}}
            @if(!$thread->is_closed)
                @auth
                    <button type="button"
                            onclick="replyTo({{ $reply->id }}, '{{ addslashes($reply->user->name) }}', '{{ addslashes($snippetText) }}')"
                            class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-800 text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:border-indigo-500/30 hover:bg-indigo-500/5 transition-all active:scale-95 duration-150">
                        <i class="fa-solid fa-reply text-xs"></i> Balas
                    </button>
                @else
                    <a href="{{ route('login') }}"
                       class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-800 text-slate-400 hover:text-indigo-600 transition-all">
                        <i class="fa-solid fa-reply text-xs"></i> Balas
                    </a>
                @endauth
            @endif

            {{-- Show children count badge if there are nested replies --}}
            @if($hasChildren && $depth === 0)
                <span class="ml-auto inline-flex items-center gap-1 text-[10px] font-bold text-violet-500 dark:text-violet-400 bg-violet-500/10 border border-violet-500/20 px-2 py-0.5 rounded-full">
                    <i class="fa-solid fa-code-branch text-[8px]"></i>
                    {{ $reply->children->count() }} balasan bersarang
                </span>
            @endif
        </div>

    </div>
</div>

{{-- ── Nested Children Replies ──────────────────────────────────────────── --}}
@if($hasChildren)
    <div class="nested-replies-container">
        @foreach($reply->children as $child)
            @include('forum.partials.reply-card', [
                'reply'         => $child,
                'thread'        => $thread,
                'likedReplyIds' => $likedReplyIds,
                'depth'         => $depth + 1,
            ])
        @endforeach
    </div>
@endif
