@extends('layouts.app')

@section('title', $thread->title . ' — DevGate Forum')

@section('styles')
<!-- Quill snow theme CDN -->
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet" />
<style>
    /* Premium tech styles for forum prose */
    .forum-prose p {
        margin-bottom: 1.25rem;
        line-height: 1.75;
        font-size: 1rem;
        color: #334155;
    }
    .dark .forum-prose p {
        color: #cbd5e1;
    }
    /* ── Inline Code ──────────────────────────────────────────────────── */
    .forum-prose :not(pre) > code {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.82em;
        padding: 0.18em 0.5em;
        border-radius: 0.4rem;
        background-color: #f1f5f9;
        color: #6366f1;
        border: 1px solid #e2e8f0;
        white-space: nowrap;
    }
    .dark .forum-prose :not(pre) > code {
        background-color: #1e293b;
        color: #818cf8;
        border-color: #334155;
    }

    /* ── Premium Code Block Wrapper ─────────────────────────────────── */
    .code-block-wrapper {
        margin: 1.5rem 0;
        border-radius: 1rem;
        overflow: hidden;
        border: 1px solid #1e293b;
        box-shadow: 0 4px 24px rgba(0,0,0,0.25), 0 0 0 1px rgba(99,102,241,0.08);
    }
    .code-block-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background-color: #161b27;
        padding: 0.5rem 1rem;
        border-bottom: 1px solid #1e293b;
        gap: 0.75rem;
    }
    .code-block-header-left {
        display: flex;
        align-items: center;
        gap: 0.65rem;
    }
    .code-dots {
        display: flex;
        gap: 5px;
        align-items: center;
        flex-shrink: 0;
    }
    .code-dots span {
        display: block;
        width: 10px;
        height: 10px;
        border-radius: 50%;
    }
    .code-dots .dot-red    { background-color: #ff5f56; }
    .code-dots .dot-yellow { background-color: #ffbd2e; }
    .code-dots .dot-green  { background-color: #27c93f; }
    .code-block-lang {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.64rem;
        font-weight: 700;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: #6366f1;
        background: rgba(99,102,241,0.1);
        border: 1px solid rgba(99,102,241,0.2);
        padding: 0.15em 0.6em;
        border-radius: 0.35rem;
    }
    .copy-code-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        font-size: 0.65rem;
        font-weight: 600;
        padding: 0.25rem 0.65rem;
        border-radius: 0.5rem;
        background-color: rgba(99, 102, 241, 0.08);
        color: #94a3b8;
        border: 1px solid rgba(99, 102, 241, 0.2);
        cursor: pointer;
        transition: all 0.15s ease;
        font-family: 'JetBrains Mono', monospace;
        flex-shrink: 0;
        user-select: none;
    }
    .copy-code-btn:hover {
        background-color: rgba(99, 102, 241, 0.18);
        color: #818cf8;
        border-color: rgba(99, 102, 241, 0.4);
    }
    .copy-code-btn.copied {
        background-color: rgba(16, 185, 129, 0.1);
        color: #10b981;
        border-color: rgba(16, 185, 129, 0.3);
    }
    .code-block-wrapper pre {
        margin: 0 !important;
        border-radius: 0 !important;
        padding: 1.25rem 1.5rem !important;
        background-color: #0d1117 !important;
        overflow-x: auto;
        border: none !important;
        font-size: 0.875rem;
        line-height: 1.7;
    }
    .code-block-wrapper pre code {
        font-family: 'JetBrains Mono', monospace;
        background: transparent !important;
        padding: 0 !important;
        border-radius: 0 !important;
        border: none !important;
        color: inherit;
        font-size: 0.875rem;
        white-space: pre;
    }
    /* Hide raw Quill ql-syntax pre before JS transforms it */
    .forum-prose pre.ql-syntax,
    .forum-prose div.ql-code-block-container {
        display: none;
    }
    
    /* Extra prose styling for Quill HTML elements */
    .forum-prose ul {
        list-style-type: disc;
        margin-left: 1.5rem;
        margin-bottom: 1.25rem;
    }
    .forum-prose ol {
        list-style-type: decimal;
        margin-left: 1.5rem;
        margin-bottom: 1.25rem;
    }
    .forum-prose li {
        margin-bottom: 0.5rem;
    }
    .forum-prose blockquote {
        border-left: 4px solid #6366f1;
        padding-left: 1rem;
        margin-bottom: 1.25rem;
        font-style: italic;
        color: #475569;
    }
    .dark .forum-prose blockquote {
        color: #94a3b8;
    }
    .forum-prose a {
        color: #6366f1;
        text-decoration: underline;
    }
    .forum-prose a:hover {
        color: #4f46e5;
    }
    .dark .forum-prose a {
        color: #818cf8;
    }
    .dark .forum-prose a:hover {
        color: #6366f1;
    }
    .forum-prose h2 {
        font-size: 1.25rem;
        font-weight: 700;
        margin-top: 1.5rem;
        margin-bottom: 0.75rem;
        color: #0f172a;
    }
    .dark .forum-prose h2 {
        color: #f8fafc;
    }
    .forum-prose h3 {
        font-size: 1.125rem;
        font-weight: 700;
        margin-top: 1.25rem;
        margin-bottom: 0.5rem;
        color: #0f172a;
    }
    .dark .forum-prose h3 {
        color: #f8fafc;
    }

    /* Custom styles for Quill editor in replies */
    .ql-toolbar.ql-snow {
        border: 1px solid rgba(226, 232, 240, 0.8) !important;
        background-color: #f8fafc !important;
        border-top-left-radius: 1rem;
        border-top-right-radius: 1rem;
        padding: 10px 15px;
    }
    .ql-container.ql-snow {
        border: 1px solid rgba(226, 232, 240, 0.8) !important;
        background-color: rgba(255, 255, 255, 0.5) !important;
        border-bottom-left-radius: 1rem;
        border-bottom-right-radius: 1rem;
        font-family: 'Outfit', sans-serif;
        font-size: 0.875rem;
    }
    #editor {
        height: 120px;
    }
    .ql-editor.ql-blank::before {
        color: #94a3b8 !important;
        font-style: normal !important;
    }
    .dark .ql-toolbar.ql-snow {
        border: 1px solid #1e293b !important;
        background-color: #0b0f19 !important;
    }
    .dark .ql-toolbar .ql-stroke {
        stroke: #94a3b8 !important;
    }
    .dark .ql-toolbar .ql-fill {
        fill: #94a3b8 !important;
    }
    .dark .ql-toolbar .ql-picker {
        color: #94a3b8 !important;
    }
    .dark .ql-toolbar .ql-picker-options {
        background-color: #0b0f19 !important;
        border-color: #1e293b !important;
    }
    .dark .ql-container.ql-snow {
        border: 1px solid #1e293b !important;
        background-color: rgba(2, 6, 23, 0.4) !important;
        color: #f8fafc;
    }

    /* ── Nested Reply mini-editor styles ──────────────────────────────── */
    .nested-editor {
        height: 90px;
    }
    .nested-reply-form {
        display: none;
        animation: slideDown 0.2s ease;
    }
    .nested-reply-form.is-open {
        display: block;
    }
    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-6px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* ── Nested Reply thread line ─────────────────────────────────────── */
    .nested-replies-container {
        position: relative;
        margin-top: 1rem;
        padding-left: 1.5rem;
    }
    .nested-replies-container::before {
        content: '';
        position: absolute;
        left: 0.5rem;
        top: 0;
        bottom: 0;
        width: 2px;
        background: linear-gradient(to bottom, #6366f1, transparent);
        border-radius: 999px;
        opacity: 0.35;
    }
    .dark .nested-replies-container::before {
        opacity: 0.2;
    }
</style>
@endsection

@section('content')
<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-10">

    {{-- Breadcrumbs & Back Link --}}
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <a href="{{ route('forum.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
            <i class="fa-solid fa-arrow-left text-xs"></i> Kembali ke Forum Diskusi
        </a>
        
        @auth
            <a href="{{ route('forum.create') }}"
               class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-xs font-bold text-white shadow-md hover:bg-indigo-500 transition-all">
                <i class="fa-solid fa-plus"></i> Buat Topik Baru
            </a>
        @endauth
    </div>

    {{-- Main Grid Layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        {{-- Thread details and replies area --}}
        <div class="lg:col-span-9 space-y-8">
            
            {{-- Topic Main Card --}}
            <article class="rounded-2xl border border-slate-200/60 dark:border-slate-800 bg-white/70 dark:bg-slate-900/50 backdrop-blur-md p-6 sm:p-8 shadow-sm">
                
                {{-- Meta / Header --}}
                <div class="flex flex-wrap items-center justify-between gap-3 mb-5 border-b border-slate-100 dark:border-slate-800/80 pb-5">
                    <div class="flex items-center gap-3">
                        <img src="{{ $thread->user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($thread->user->name).'&background=6366f1&color=fff&size=80' }}"
                             alt="{{ $thread->user->name }}"
                             class="h-11 w-11 rounded-xl object-cover ring-1 ring-slate-200 dark:ring-slate-700">
                        <div>
                            <span class="text-sm font-bold text-slate-800 dark:text-slate-200 block leading-tight">{{ $thread->user->name }}</span>
                            <span class="text-xs text-slate-400 dark:text-slate-500 mt-0.5 block">
                                {{ $thread->created_at->diffForHumans() }} · <i class="fa-regular fa-eye ml-1"></i> {{ $thread->views }} tayangan
                            </span>
                        </div>
                    </div>
                    
                    {{-- Status Badge --}}
                    @if($thread->is_solved)
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-900/60 px-3 py-1 text-xs font-bold text-emerald-700 dark:text-emerald-400">
                            <i class="fa-solid fa-circle-check"></i> Terjawab / Solved
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 dark:bg-amber-950/50 border border-amber-200 dark:border-amber-900/60 px-3 py-1 text-xs font-bold text-amber-700 dark:text-amber-400">
                            <i class="fa-solid fa-circle-question"></i> Belum Terjawab
                        </span>
                    @endif
                </div>

                {{-- Title --}}
                <h1 class="text-xl sm:text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white leading-snug mb-5">
                    {{ $thread->title }}
                </h1>

                {{-- Body content --}}
                <div class="forum-prose text-slate-700 dark:text-slate-300 leading-relaxed break-words">
                    {!! $thread->body !!}
                </div>

                {{-- Tags list --}}
                @if($thread->tags)
                    <div class="mt-6 pt-5 border-t border-slate-100 dark:border-slate-800/80 flex flex-wrap gap-2">
                        @foreach($thread->tags as $tag)
                            <a href="{{ route('forum.index', ['tag' => $tag]) }}" 
                               class="inline-flex items-center gap-1 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700/80 px-2.5 py-1 text-xs font-semibold text-slate-600 dark:text-slate-400 transition-colors">
                                <i class="fa-solid fa-tag text-[10px] text-slate-400"></i> {{ $tag }}
                            </a>
                        @endforeach
                    </div>
                @endif

            </article>

            {{-- Replies Header --}}
            <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3" id="replies">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="fa-regular fa-comments text-indigo-500"></i>
                    {{ $thread->replies->count() }} Balasan
                </h2>
            </div>

            {{-- Replies Stream --}}
            <div class="space-y-6" id="replies-stream">
                @forelse($thread->replies as $reply)
                    @include('forum.partials.reply-card', ['reply' => $reply, 'thread' => $thread, 'likedReplyIds' => $likedReplyIds, 'depth' => 0])
                @empty
                    <div class="rounded-2xl border border-dashed border-slate-300 dark:border-slate-800 p-12 text-center bg-white/30 dark:bg-slate-900/10">
                        <i class="fa-regular fa-comment-dots text-3xl text-slate-300 dark:text-slate-700 mb-3 block"></i>
                        <p class="font-bold text-slate-500 dark:text-slate-400 text-sm">Belum ada tanggapan</p>
                        <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Jadilah yang pertama memberikan solusi atau opini!</p>
                    </div>
                @endforelse
            </div>

            {{-- New Reply Box --}}
            <div class="rounded-2xl border border-slate-200/60 dark:border-slate-800 bg-white/70 dark:bg-slate-900/50 backdrop-blur-md p-6 sm:p-8 shadow-sm" id="reply-form-section">
                @auth
                    <h3 class="text-base font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-reply text-indigo-500"></i> Kirim Tanggapan Anda
                    </h3>
                    
                    {{-- Context banner: shown when replying to a specific reply --}}
                    <div id="reply-context-banner" class="hidden mb-4 flex items-start gap-3 rounded-xl bg-indigo-500/5 border border-indigo-500/20 p-3">
                        <i class="fa-solid fa-quote-left text-indigo-400 mt-0.5 text-sm"></i>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-bold text-indigo-600 dark:text-indigo-400 mb-0.5">Membalas komentar:</p>
                            <p id="reply-context-text" class="text-xs text-slate-500 dark:text-slate-400 truncate"></p>
                        </div>
                        <button type="button" onclick="cancelNestedReply()" class="text-slate-400 hover:text-rose-500 transition-colors flex-shrink-0">
                            <i class="fa-solid fa-xmark text-sm"></i>
                        </button>
                    </div>

                    <form action="{{ route('forum.replies.store', $thread->id) }}" method="POST" id="forum-reply-form" class="space-y-4">
                        @csrf
                        <input type="hidden" name="parent_reply_id" id="parent_reply_id" value="">
                        <div>
                            {{-- Rich editor container --}}
                            <div id="editor"></div>
                            
                            {{-- Hidden body input --}}
                            <input type="hidden" name="body" id="body_input" value="{{ old('body') }}">
                            
                            <div class="mt-2 flex items-center justify-between text-xs text-slate-400 dark:text-slate-500">
                                <span class="flex items-center gap-1">
                                    <i class="fa-solid fa-circle-info text-indigo-500 text-xs"></i> 
                                    Bantu sesama pengembang dengan menyertakan instruksi penyelesaian yang sistematis. (Min. 10 karakter)
                                </span>
                                <span id="char-count">0 karakter</span>
                            </div>
                        </div>
                        <div class="flex justify-end pt-2">
                            <button type="submit" id="submit-reply-btn"
                                    class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-indigo-600 to-violet-600 px-5 py-2.5 text-xs font-bold text-white shadow-md shadow-indigo-500/20 hover:shadow-indigo-500/30 transition-all">
                                <i class="fa-regular fa-paper-plane"></i> <span id="submit-label">Kirim Jawaban</span>
                            </button>
                        </div>
                    </form>
                @else
                    <div class="text-center py-6">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-500/10 text-indigo-500 dark:text-indigo-400 mx-auto mb-4">
                            <i class="fa-solid fa-right-to-bracket text-lg"></i>
                        </div>
                        <h4 class="text-sm font-bold text-slate-800 dark:text-slate-200">Bergabung dalam Diskusi</h4>
                        <p class="text-xs text-slate-400 dark:text-slate-500 mt-1 max-w-sm mx-auto">
                            Anda harus masuk ke akun Anda terlebih dahulu untuk membalas, menyukai tanggapan, atau memposting topik baru.
                        </p>
                        <div class="mt-4 flex items-center justify-center gap-3">
                            <a href="{{ route('login') }}" class="rounded-xl border border-slate-200 dark:border-slate-800 bg-transparent px-4 py-2 text-xs font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all">
                                Masuk
                            </a>
                            <a href="{{ route('register') }}" class="rounded-xl bg-indigo-600 px-4 py-2 text-xs font-bold text-white shadow-md hover:bg-indigo-500 transition-all">
                                Daftar
                            </a>
                        </div>
                    </div>
                @endauth
            </div>

        </div>

        {{-- Sidebar --}}
        <div class="lg:col-span-3 space-y-6">
            
            {{-- Creator card --}}
            <div class="rounded-2xl border border-slate-200/60 dark:border-slate-800 bg-white/70 dark:bg-slate-900/50 backdrop-blur-md p-5 shadow-sm text-center">
                <h3 class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-3">Pembuat Topik</h3>
                
                <img src="{{ $thread->user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($thread->user->name).'&background=6366f1&color=fff&size=120' }}"
                     alt="{{ $thread->user->name }}"
                     class="h-16 w-16 rounded-2xl object-cover ring-2 ring-indigo-500/10 mx-auto mb-3">
                     
                <span class="text-sm font-bold text-slate-800 dark:text-slate-200 block mb-1">{{ $thread->user->name }}</span>
                <span class="text-xs text-slate-400 block mb-3">Member sejak {{ $thread->user->created_at->format('M Y') }}</span>
                
                @if($thread->user->username)
                    <a href="{{ route('blog.author', $thread->user->username) }}" class="inline-flex w-full items-center justify-center gap-1.5 rounded-xl border border-slate-200 dark:border-slate-800 px-3 py-2 text-xs font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all">
                        <i class="fa-regular fa-newspaper"></i> Kunjungi Blog Penulis
                    </a>
                @endif
            </div>

            {{-- Topic stats --}}
            <div class="rounded-2xl border border-slate-200/60 dark:border-slate-800 bg-white/70 dark:bg-slate-900/50 backdrop-blur-md p-5 shadow-sm">
                <h3 class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-4">Informasi Topik</h3>
                
                <div class="space-y-3.5 text-xs">
                    <div class="flex items-center justify-between text-slate-500 dark:text-slate-400">
                        <span>Dibuat:</span>
                        <span class="font-bold text-slate-800 dark:text-slate-200">{{ $thread->created_at->format('d M Y, H:i') }}</span>
                    </div>
                    <div class="flex items-center justify-between text-slate-500 dark:text-slate-400">
                        <span>Status Solusi:</span>
                        @if($thread->is_solved)
                            <span class="font-extrabold text-emerald-500">Selesai (Solved)</span>
                        @else
                            <span class="font-extrabold text-amber-500">Aktif</span>
                        @endif
                    </div>
                    <div class="flex items-center justify-between text-slate-500 dark:text-slate-400">
                        <span>Total Balasan:</span>
                        <span class="font-bold text-slate-800 dark:text-slate-200">{{ $thread->replies->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between text-slate-500 dark:text-slate-400">
                        <span>Dibaca:</span>
                        <span class="font-bold text-slate-800 dark:text-slate-200">{{ $thread->views }} kali</span>
                    </div>
                </div>
            </div>

            {{-- Rules / Tips --}}
            <div class="rounded-2xl border border-slate-200/60 dark:border-slate-800 bg-white/70 dark:bg-slate-900/50 backdrop-blur-md p-5 shadow-sm">
                <h3 class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-3 flex items-center gap-1.5">
                    <i class="fa-solid fa-shield-halved text-cyan-500"></i> Bimbingan Menjawab
                </h3>
                <ul class="space-y-2 text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed pl-3 list-disc">
                    <li>Fokus pada pemecahan masalah teknis.</li>
                    <li>Sertakan potongan kode program jika relevan.</li>
                    <li>Hindari jawaban yang menyinggung atau meremehkan.</li>
                    <li>Topik ini bersifat publik dan dibaca ribuan developer lain.</li>
                </ul>
            </div>

        </div>

    </div>
</div>
@endsection

@section('scripts')
@auth
<!-- Quill JS Library from CDN -->
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
@endauth
<script>
    function initCodeBlocks() {
        // ── Transform Quill and regular code blocks into premium styled blocks ──────
        const forumProseElements = document.querySelectorAll('.forum-prose');

        const langLabels = {
            'javascript': 'JavaScript', 'js': 'JavaScript',
            'typescript': 'TypeScript', 'ts': 'TypeScript',
            'php': 'PHP',
            'python': 'Python', 'py': 'Python',
            'html': 'HTML', 'xml': 'XML',
            'css': 'CSS', 'scss': 'SCSS', 'less': 'Less',
            'bash': 'Bash', 'shell': 'Shell', 'sh': 'Shell',
            'sql': 'SQL',
            'json': 'JSON',
            'yaml': 'YAML', 'yml': 'YAML',
            'c': 'C', 'cpp': 'C++', 'csharp': 'C#',
            'java': 'Java',
            'go': 'Go',
            'rust': 'Rust',
            'ruby': 'Ruby', 'rb': 'Ruby',
            'kotlin': 'Kotlin',
            'swift': 'Swift',
            'arduino': 'Arduino',
            'makefile': 'Makefile',
            'dockerfile': 'Dockerfile',
            'nginx': 'Nginx',
            'apache': 'Apache',
            'markdown': 'Markdown',
            'ini': 'INI / Config',
            'plaintext': 'Text', 'text': 'Text',
        };

        forumProseElements.forEach(container => {
            // Support both pre.ql-syntax (standard Quill pre) and div.ql-code-block-container (Quill 2.x div wrapper)
            const quillCodeContainers = container.querySelectorAll('pre.ql-syntax, div.ql-code-block-container');

            quillCodeContainers.forEach(codeContainer => {
                try {
                    let rawCode = '';
                    
                    if (codeContainer.tagName.toLowerCase() === 'pre') {
                        rawCode = codeContainer.textContent;
                    } else {
                        // It's a div.ql-code-block-container
                        const lines = Array.from(codeContainer.querySelectorAll('.ql-code-block')).map(el => {
                            // Handle potential empty/blank lines represented by br inside ql-code-block
                            if (el.innerHTML === '<br>' || el.innerHTML === '') {
                                return '';
                            }
                            return el.textContent;
                        });
                        rawCode = lines.join('\n');
                    }

                    // Use hljs to auto-detect language & highlight
                    let highlighted, detectedLang;
                    if (typeof hljs !== 'undefined') {
                        const result = hljs.highlightAuto(rawCode);
                        highlighted = result.value;
                        detectedLang = result.language || 'plaintext';
                    } else {
                        // Fallback: escape HTML only
                        highlighted = rawCode
                            .replace(/&/g, '&amp;')
                            .replace(/</g, '&lt;')
                            .replace(/>/g, '&gt;');
                        detectedLang = 'plaintext';
                    }

                    const langLabel = langLabels[detectedLang] || (detectedLang.charAt(0).toUpperCase() + detectedLang.slice(1));

                    // Build wrapper
                    const wrapper = document.createElement('div');
                    wrapper.className = 'code-block-wrapper';

                    wrapper.innerHTML = `
                        <div class="code-block-header">
                            <div class="code-block-header-left">
                                <div class="code-dots">
                                    <span class="dot-red"></span>
                                    <span class="dot-yellow"></span>
                                    <span class="dot-green"></span>
                                </div>
                                <span class="code-block-lang">${langLabel}</span>
                            </div>
                            <button type="button" class="copy-code-btn" onclick="copyCodeBlock(this)">
                                <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>
                                Salin Kode
                            </button>
                        </div>
                        <pre><code class="hljs language-${detectedLang}">${highlighted}</code></pre>
                    `;

                    // Replace original codeContainer with premium wrapper
                    codeContainer.parentNode.replaceChild(wrapper, codeContainer);
                } catch (e) {
                    console.error('Error formatting code container:', e);
                    // Gracefully unhide raw container on crash
                    codeContainer.style.display = 'block';
                }
            });

            // Also handle regular <pre><code> blocks not from Quill (non ql-syntax)
            const regularPres = Array.from(container.querySelectorAll('pre')).filter(pre => {
                return !pre.closest('.code-block-wrapper') && !pre.classList.contains('ql-syntax');
            });

            regularPres.forEach(pre => {
                try {
                    const codeEl = pre.querySelector('code');
                    if (!codeEl) return;

                    if (typeof hljs !== 'undefined') {
                        hljs.highlightElement(codeEl);
                    }

                    const detectedLang = (codeEl.dataset.highlighted && codeEl.className.match(/language-(\w+)/)?.[1]) || 'plaintext';
                    const langLabel = langLabels[detectedLang] || (detectedLang.charAt(0).toUpperCase() + detectedLang.slice(1));

                    // Wrap in premium container
                    const wrapper = document.createElement('div');
                    wrapper.className = 'code-block-wrapper';

                    const header = document.createElement('div');
                    header.className = 'code-block-header';
                    header.innerHTML = `
                        <div class="code-block-header-left">
                            <div class="code-dots">
                                <span class="dot-red"></span>
                                <span class="dot-yellow"></span>
                                <span class="dot-green"></span>
                            </div>
                            <span class="code-block-lang">${langLabel}</span>
                        </div>
                        <button type="button" class="copy-code-btn" onclick="copyCodeBlock(this)">
                            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>
                            Salin Kode
                        </button>
                    `;

                    pre.parentNode.insertBefore(wrapper, pre);
                    wrapper.appendChild(header);
                    wrapper.appendChild(pre);
                } catch (e) {
                    console.error('Error formatting regular pre block:', e);
                }
            });
        });
    }

    // Initialize with safe check for document loading state to prevent timing issues
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCodeBlocks);
    } else {
        initCodeBlocks();
    }

    document.addEventListener('DOMContentLoaded', function() {
        @auth
        // Initialize Quill for replies
        const quill = new Quill('#editor', {
            theme: 'snow',
            placeholder: 'Tuliskan saran pemecahan masalah, tips, atau koreksi secara terperinci...',
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline', 'blockquote'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    ['link', 'code-block'],
                    ['clean']
                ]
            }
        });

        // Store quill instance globally for nested reply functions
        window._forumQuill = quill;

        // Set initial body value if available (e.g. on validation fallback)
        const oldBodyInput = document.getElementById('body_input');
        if (oldBodyInput && oldBodyInput.value) {
            quill.root.innerHTML = oldBodyInput.value;
        }

        // Live Character Counting
        const charCount = document.getElementById('char-count');
        function updateCharCount() {
            const text = quill.getText().trim();
            const count = text.length;
            charCount.textContent = count + ' karakter';
            
            if (count < 10) {
                charCount.classList.add('text-rose-500');
                charCount.classList.remove('text-slate-400', 'dark:text-slate-500', 'text-emerald-500');
            } else {
                charCount.classList.remove('text-rose-500');
                charCount.classList.add('text-emerald-500');
            }
        }
        quill.on('text-change', updateCharCount);
        updateCharCount(); // Initialize count

        // Sync Quill HTML content to hidden input text field on form submit
        const form = document.getElementById('forum-reply-form');
        if (form) {
            form.addEventListener('submit', function(e) {
                const editorContent = quill.root.innerHTML;
                
                // Robust check to see if Quill is truly empty
                const isEditorEmpty = quill.getLength() <= 1 || 
                                       editorContent === '<p><br></p>' || 
                                       editorContent === '<p></p>' || 
                                       editorContent === '';
                
                if (isEditorEmpty) {
                    oldBodyInput.value = '';
                } else {
                    oldBodyInput.value = editorContent;
                }
            });
        }
        @endauth

    });

    /**
     * Activate nested reply mode: set parent_reply_id and show context banner.
     * @param {number} replyId - The ID of the reply being responded to.
     * @param {string} replyAuthor - The name of the reply author.
     * @param {string} replySnippet - A short preview of the reply body.
     */
    function replyTo(replyId, replyAuthor, replySnippet) {
        @guest
            window.location.href = "{{ route('login') }}";
            return;
        @endguest

        // Update hidden field
        document.getElementById('parent_reply_id').value = replyId;

        // Show context banner
        const banner = document.getElementById('reply-context-banner');
        const contextText = document.getElementById('reply-context-text');
        banner.classList.remove('hidden');
        contextText.textContent = replyAuthor + ': "' + replySnippet + '"';

        // Update submit label
        document.getElementById('submit-label').textContent = 'Kirim Balasan';

        // Scroll to reply form and focus editor
        const replySection = document.getElementById('reply-form-section');
        replySection.scrollIntoView({ behavior: 'smooth', block: 'center' });
        setTimeout(() => {
            if (window._forumQuill) window._forumQuill.focus();
        }, 400);
    }

    /**
     * Cancel nested reply mode, reset to normal reply.
     */
    function cancelNestedReply() {
        document.getElementById('parent_reply_id').value = '';
        document.getElementById('reply-context-banner').classList.add('hidden');
        document.getElementById('reply-context-text').textContent = '';
        document.getElementById('submit-label').textContent = 'Kirim Jawaban';
    }

    /**
     * AJAX Toggle Like logic for Replies
     */
    function toggleLike(replyId, btn) {
        @guest
            window.location.href = "{{ route('login') }}";
            return;
        @endguest

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Disable temporarily to prevent double click
        btn.disabled = true;

        fetch(`/forum/replies/${replyId}/like`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Gagal memproses like');
            }
            return response.json();
        })
        .then(data => {
            // data contains: { liked: true/false, count: X }
            const likeCountEl = btn.querySelector('.like-count');
            const heartIcon = btn.querySelector('.fa-solid');

            likeCountEl.textContent = data.count;

            if (data.liked) {
                // Liked state
                btn.classList.add('bg-rose-500/10', 'text-rose-500', 'border-rose-500/30');
                btn.classList.remove('border-slate-200', 'dark:border-slate-800', 'text-slate-400');
                heartIcon.classList.add('scale-110');
            } else {
                // Unliked state
                btn.classList.remove('bg-rose-500/10', 'text-rose-500', 'border-rose-500/30');
                btn.classList.add('border-slate-200', 'dark:border-slate-800', 'text-slate-400');
                heartIcon.classList.remove('scale-110');
            }
        })
        .catch(err => {
            console.error(err);
            alert('Terjadi kesalahan saat menyukai balasan. Coba lagi.');
        })
        .finally(() => {
            btn.disabled = false;
        });
    }

    /**
     * Copy the code inside a code block to clipboard.
     * @param {HTMLElement} btn - The copy button element clicked.
     */
    function copyCodeBlock(btn) {
        const wrapper = btn.closest('.code-block-wrapper');
        const codeEl  = wrapper ? wrapper.querySelector('code') : null;
        if (!codeEl) return;

        const text = codeEl.innerText || codeEl.textContent;
        navigator.clipboard.writeText(text).then(() => {
            btn.classList.add('copied');
            btn.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                Tersalin!
            `;
            setTimeout(() => {
                btn.classList.remove('copied');
                btn.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>
                    Salin Kode
                `;
            }, 2000);
        }).catch(() => {
            // Fallback for older browsers
            const ta = document.createElement('textarea');
            ta.value = text;
            ta.style.position = 'fixed';
            ta.style.opacity = '0';
            document.body.appendChild(ta);
            ta.select();
            document.execCommand('copy');
            document.body.removeChild(ta);
        });
    }
</script>
@endsection
