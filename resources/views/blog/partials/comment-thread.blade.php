<div class="flex flex-col gap-3" x-data="{ replyOpen: false }">
    <div class="flex gap-3">
        <!-- Commenter Avatar -->
        <div class="flex-shrink-0">
            @if($comment->user_id && $comment->user && $comment->user->avatar)
                <img src="{{ Storage::url($comment->user->avatar) }}" alt="{{ $comment->user->name }}" class="h-8 w-8 rounded-lg object-cover">
            @else
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-200 dark:bg-slate-800 text-[10px] font-semibold text-slate-500">
                    {{ strtoupper(substr($comment->user_id ? $comment->user->name : $comment->guest_name, 0, 2)) }}
                </div>
            @endif
        </div>

        <!-- Comment Body Block -->
        <div class="flex-grow min-w-0 rounded-2xl border border-slate-100 bg-slate-50/50 p-4 dark:border-slate-850 dark:bg-slate-950/20">
            <div class="flex items-center justify-between gap-4 mb-1">
                <div>
                    <span class="text-xs font-bold text-slate-800 dark:text-slate-200">
                        {{ $comment->user_id ? $comment->user->name : $comment->guest_name }}
                    </span>
                    @if($comment->user_id)
                        <span class="text-[9px] rounded bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 px-1 py-0.5 ml-1 font-semibold">Verified Member</span>
                    @else
                        <span class="text-[9px] rounded bg-slate-100 dark:bg-slate-800 text-slate-500 px-1 py-0.5 ml-1 font-semibold">Tamu</span>
                    @endif
                </div>
                <span class="text-[10px] text-slate-400">{{ $comment->created_at->diffForHumans() }}</span>
            </div>
            
            <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed font-light mt-1.5">
                {!! nl2br(e(strip_tags($comment->body))) !!}
            </p>

            <!-- Inline Utilities: Reply trigger button -->
            <div class="flex items-center gap-4 mt-3">
                <button @click="replyOpen = !replyOpen" class="inline-flex items-center gap-1 text-[10px] font-bold text-slate-500 hover:text-indigo-600 transition-colors">
                    <i class="fa-regular fa-comment-dots text-xs"></i> Balas
                </button>
            </div>
        </div>
    </div>

    <!-- Inline Reply Form (Alpine toggled) -->
    <div x-show="replyOpen" x-cloak x-collapse class="pl-11 mt-2">
        <form action="{{ route('blog.comments.store', $article->id) }}" method="POST" class="flex flex-col gap-3 rounded-2xl border border-slate-100 bg-white dark:border-slate-800 dark:bg-slate-900/30 p-4">
            @csrf
            <input type="hidden" name="parent_id" value="{{ $comment->id }}">
            
            @guest
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="flex flex-col gap-1">
                        <label class="text-[10px] font-semibold text-slate-400">Nama Lengkap *</label>
                        <input type="text" name="guest_name" required class="rounded-xl border border-slate-200 bg-white/50 px-3 py-1.5 text-xs dark:border-slate-800 dark:bg-slate-950/40 focus:border-indigo-500 focus:outline-none">
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-[10px] font-semibold text-slate-400">Email *</label>
                        <input type="email" name="guest_email" required class="rounded-xl border border-slate-200 bg-white/50 px-3 py-1.5 text-xs dark:border-slate-800 dark:bg-slate-950/40 focus:border-indigo-500 focus:outline-none">
                    </div>
                </div>
            @endguest

            <div class="flex flex-col gap-1">
                <label class="text-[10px] font-semibold text-slate-400">Pesan Balasan *</label>
                <textarea name="body" required rows="2" placeholder="Tulis balasan Anda..." class="rounded-xl border border-slate-200 bg-white/50 px-3 py-1.5 text-xs dark:border-slate-800 dark:bg-slate-950/40 focus:border-indigo-500 focus:outline-none"></textarea>
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" @click="replyOpen = false" class="rounded-lg border border-slate-200 px-3 py-1.5 text-[10px] font-bold text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-400 dark:hover:bg-slate-800">
                    Batal
                </button>
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-1.5 text-[10px] font-bold text-white hover:bg-indigo-500">
                    Kirim Balasan
                </button>
            </div>
        </form>
    </div>

    <!-- Recursive rendering for child replies -->
    @if($comment->replies && $comment->replies->count() > 0)
        <div class="pl-6 sm:pl-11 mt-1 border-l border-slate-200/60 dark:border-slate-800/80 flex flex-col gap-4">
            @foreach($comment->replies as $reply)
                @include('blog.partials.comment-thread', ['comment' => $reply])
            @endforeach
        </div>
    @endif
</div>
