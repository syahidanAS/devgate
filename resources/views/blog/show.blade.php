@extends('layouts.app')

@section('title', $article->title . ' — DevGate Blog')

@section('meta')
    <meta name="description" content="{{ $article->meta_description ?? $article->excerpt }}">
    <meta property="og:title" content="{{ $article->meta_title ?? $article->title }}">
    <meta property="og:description" content="{{ $article->meta_description ?? $article->excerpt }}">
    <meta property="og:image" content="{{ $article->thumbnail_url }}">
    <meta property="og:type" content="article">
@endsection

@section('styles')
<style>
    /* Styling for the rich text blog content */
    .blog-prose h2 {
        font-size: 1.875rem;
        font-weight: 800;
        margin-top: 2.75rem;
        margin-bottom: 1.25rem;
        line-height: 1.3;
        color: #0f172a;
        scroll-margin-top: 100px; /* Offset for sticky header */
    }
    .dark .blog-prose h2 {
        color: #f8fafc;
    }
    .blog-prose h3 {
        font-size: 1.5rem;
        font-weight: 700;
        margin-top: 2.25rem;
        margin-bottom: 1rem;
        line-height: 1.35;
        color: #1e293b;
        scroll-margin-top: 100px;
    }
    .dark .blog-prose h3 {
        color: #f1f5f9;
    }
    .blog-prose p {
        margin-bottom: 1.5rem;
        line-height: 1.85;
        font-size: 1.125rem; /* Generous 18px reading size */
        font-weight: 400; /* Richer weight for high contrast */
        color: #334155; /* Slate-750 for crisp readability */
    }
    .dark .blog-prose p {
        color: #cbd5e1; /* Lighter gray for dark mode readability */
    }
    .blog-prose ul {
        list-style-type: disc;
        padding-left: 1.75rem;
        margin-bottom: 1.5rem;
        color: #334155;
    }
    .dark .blog-prose ul {
        color: #cbd5e1;
    }
    .blog-prose ol {
        list-style-type: decimal;
        padding-left: 1.75rem;
        margin-bottom: 1.5rem;
        color: #334155;
    }
    .dark .blog-prose ol {
        color: #cbd5e1;
    }
    .blog-prose li {
        margin-bottom: 0.625rem;
        line-height: 1.8;
        font-size: 1.125rem;
    }
    .blog-prose blockquote {
        border-left: 4px solid #6366f1;
        padding: 0.5rem 0 0.5rem 1.5rem;
        font-style: italic;
        color: #475569;
        margin: 2rem 0;
        font-size: 1.25rem;
        line-height: 1.8;
        background-color: rgba(99, 102, 241, 0.03);
        border-radius: 0 1rem 1rem 0;
    }
    .dark .blog-prose blockquote {
        color: #94a3b8;
        background-color: rgba(99, 102, 241, 0.05);
    }

    /* ── Inline Code ──────────────────────────────────────────────────── */
    .blog-prose :not(pre) > code {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.82em;
        padding: 0.18em 0.5em;
        border-radius: 0.4rem;
        background-color: #f1f5f9;
        color: #6366f1;
        border: 1px solid #e2e8f0;
        white-space: nowrap;
    }
    .dark .blog-prose :not(pre) > code {
        background-color: #1e293b;
        color: #818cf8;
        border-color: #334155;
    }

    .blog-prose img {
        border-radius: 1.25rem;
        margin: 2rem auto;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
        border: 1px solid rgba(226, 232, 240, 0.8);
    }
    .dark .blog-prose img {
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3), 0 10px 10px -5px rgba(0, 0, 0, 0.3);
        border: 1px solid rgba(30, 41, 59, 0.8);
    }

    /* ── Premium Code Block Wrapper ─────────────────────────────────── */
    .code-block-wrapper {
        margin: 2rem 0;
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
    /* Hide raw Quill ql-syntax pre and ql-code-block-container before JS transforms it */
    .blog-prose pre.ql-syntax,
    .blog-prose div.ql-code-block-container {
        display: none;
    }
</style>
@endsection
@section('content')
<div class="py-8 sm:py-12" x-data="{ focusMode: false }">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        
        <!-- Navigation Breadcrumbs -->
        <nav class="flex text-xs font-medium text-slate-400 dark:text-slate-500 mb-6 gap-2 items-center" x-show="!focusMode" x-transition>
            <a href="/" class="hover:text-indigo-500 transition-colors">Beranda</a>
            <i class="fa-solid fa-chevron-right text-[8px]"></i>
            <a href="{{ route('blog.index') }}" class="hover:text-indigo-500 transition-colors">Blog</a>
            <i class="fa-solid fa-chevron-right text-[8px]"></i>
            <span class="text-slate-700 dark:text-slate-300 line-clamp-1">{{ $article->title }}</span>
        </nav>

        <!-- Main Layout Split Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Main Blog Reading Area (Col span 9, or Col span 12 centered in Focus Mode) -->
            <article 
                class="transition-all duration-500 flex flex-col gap-6"
                :class="focusMode ? 'lg:col-span-12 max-w-4xl mx-auto w-full' : 'lg:col-span-9'"
            >
                
                <!-- Main Header Meta Information -->
                <div class="rounded-3xl border border-slate-200/60 bg-white/70 p-6 sm:p-8 shadow-sm dark:border-slate-800/80 dark:bg-slate-900/40 backdrop-blur-sm">
                    <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
                        <div class="flex flex-wrap items-center gap-3 text-xs">
                            <a href="{{ route('blog.index', ['category' => $article->category->slug]) }}" class="rounded-lg bg-indigo-600 px-3 py-1 font-bold uppercase tracking-wider text-white">
                                {{ $article->category->name }}
                            </a>
                            <span class="text-slate-400 dark:text-slate-500">
                                <i class="fa-solid fa-calendar-days mr-1"></i> {{ $article->published_at?->format('d F Y') }}
                            </span>
                            <span class="text-slate-400 dark:text-slate-500">&bull;</span>
                            <span class="text-slate-400 dark:text-slate-500">
                                <i class="fa-regular fa-clock mr-1"></i> {{ $article->reading_time_text }}
                            </span>
                            <span class="text-slate-400 dark:text-slate-500">&bull;</span>
                            <span class="text-slate-400 dark:text-slate-500">
                                <i class="fa-regular fa-eye mr-1"></i> {{ number_format($article->view_count) }} views
                            </span>
                        </div>
                        
                        <!-- Toggle Focus Mode (Fullscreen) Button -->
                        <button 
                            type="button"
                            @click="focusMode = !focusMode" 
                            class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-white/80 hover:bg-slate-50 text-xs font-bold text-slate-500 dark:bg-slate-900/60 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-white transition-all shadow-sm"
                            aria-label="Toggle Fullscreen Focus Mode"
                        >
                            <span x-show="!focusMode" class="flex items-center gap-1"><i class="fa-solid fa-expand text-indigo-500 text-[10px]"></i> Mode Baca Fullscreen</span>
                            <span x-show="focusMode" class="flex items-center gap-1" x-cloak><i class="fa-solid fa-compress text-indigo-500 text-[10px]"></i> Mode Normal</span>
                        </button>
                    </div>

                    <h1 class="text-2xl sm:text-4xl font-extrabold tracking-tight text-slate-900 dark:text-white leading-tight">
                        {{ $article->title }}
                    </h1>

                    <p class="mt-4 text-base sm:text-lg text-slate-500 dark:text-slate-400 leading-relaxed font-light italic border-l-4 border-indigo-500/50 pl-4">
                        {{ $article->excerpt }}
                    </p>

                    <!-- Author Details Card -->
                    <div class="flex items-center gap-3 mt-6 border-t border-slate-100 dark:border-slate-800/80 pt-5">
                        @if($article->author->avatar)
                            <img src="{{ Storage::url($article->author->avatar) }}" alt="{{ $article->author->name }}" class="h-10 w-10 rounded-xl object-cover">
                        @else
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-600 text-sm font-semibold text-white">
                                {{ strtoupper(substr($article->author->name, 0, 2)) }}
                            </div>
                        @endif
                        <div>
                            <span class="text-xs text-slate-400 block">Ditulis oleh</span>
                            <a href="{{ route('blog.author', $article->author->username) }}" class="text-sm font-bold text-slate-800 dark:text-slate-200 hover:text-indigo-600 transition-colors">{{ $article->author->name }}</a>
                        </div>
                    </div>
                </div>

                <!-- Featured Image -->
                <div class="rounded-3xl overflow-hidden shadow-lg border border-slate-200/50 dark:border-slate-800/80 bg-slate-100 dark:bg-slate-900 max-h-[460px]">
                    <img src="{{ $article->thumbnail_url }}" alt="{{ $article->title }}" class="w-full h-full object-cover">
                </div>

                <!-- Blog Prose Article Body -->
                <div class="rounded-3xl border border-slate-200/60 bg-white/70 p-6 sm:p-10 shadow-sm dark:border-slate-800/80 dark:bg-slate-900/40 backdrop-blur-sm">
                    <div id="blog-content" class="blog-prose text-slate-700 dark:text-slate-300">
                        {!! $article->body !!}
                    </div>

                    <!-- Tags list -->
                    @if($article->tags->count() > 0)
                        <div class="mt-8 border-t border-slate-100 dark:border-slate-800 pt-6 flex flex-wrap gap-2">
                            @foreach($article->tags as $tag)
                                <a href="{{ route('blog.index', ['tag' => $tag->slug]) }}" class="text-xs px-3 py-1.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-600 hover:bg-slate-100 hover:text-indigo-600 dark:border-slate-800 dark:bg-slate-900/30 dark:text-slate-400 dark:hover:text-indigo-400 transition-all">
                                    #{{ $tag->name }}
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Cross-Promotion: Recommending Related Products -->
                @if($article->relatedProducts->count() > 0)
                    <div class="rounded-3xl border border-slate-200/60 bg-white/70 p-6 sm:p-8 shadow-sm dark:border-slate-800/80 dark:bg-slate-900/40 backdrop-blur-sm">
                        <h3 class="text-lg font-bold tracking-tight text-slate-900 dark:text-white mb-6 flex items-center gap-2">
                            <i class="fa-solid fa-microchip text-indigo-500"></i> Rekomendasi Komponen Elektronik
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach($article->relatedProducts as $product)
                                <div class="flex items-center gap-4 rounded-2xl border border-slate-100 bg-white/50 p-4 dark:border-slate-800 dark:bg-slate-950/30 group hover:border-indigo-500/30 transition-all">
                                    <div class="h-16 w-16 rounded-xl overflow-hidden bg-slate-100 dark:bg-slate-800 flex-shrink-0">
                                        @if($product->thumbnail)
                                            <img src="{{ Storage::url($product->thumbnail) }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                                        @else
                                            <div class="flex h-full w-full items-center justify-center bg-slate-200 text-slate-400"><i class="fa-solid fa-box"></i></div>
                                        @endif
                                    </div>
                                    <div class="flex-grow min-w-0">
                                        <h4 class="text-sm font-bold text-slate-800 dark:text-slate-200 group-hover:text-indigo-500 transition-colors truncate">
                                            <a href="{{ route('shop.show', $product->slug) }}">{{ $product->name }}</a>
                                        </h4>
                                        <span class="text-xs text-emerald-500 font-semibold mt-1 block">Rp {{ number_format($product->effective_price, 0, ',', '.') }}</span>
                                    </div>
                                    <form action="{{ route('cart.add', $product->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white transition-colors shadow-md shadow-indigo-600/10">
                                            <i class="fa-solid fa-cart-plus text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Comments Section -->
                @if($article->allow_comments)
                    <div class="rounded-3xl border border-slate-200/60 bg-white/70 p-6 sm:p-8 shadow-sm dark:border-slate-800/80 dark:bg-slate-900/40 backdrop-blur-sm">
                        <h3 class="text-lg font-bold tracking-tight text-slate-900 dark:text-white mb-6 flex items-center gap-2">
                            <i class="fa-regular fa-comments text-indigo-500"></i> Komunitas Komentar
                        </h3>

                        <!-- Nested Comment Stream -->
                        <div class="flex flex-col gap-6 mb-8">
                            @forelse($article->approvedComments as $comment)
                                @include('blog.partials.comment-thread', ['comment' => $comment])
                            @empty
                                <div class="text-center py-6 text-slate-400 dark:text-slate-500">
                                    <p class="text-sm">Belum ada komentar untuk artikel ini. Jadilah yang pertama memberikan tanggapan!</p>
                                </div>
                            @endforelse
                        </div>

                        <!-- Main Create Comment Form -->
                        <div class="border-t border-slate-100 dark:border-slate-800/80 pt-6">
                            <h4 class="text-sm font-bold text-slate-800 dark:text-slate-200 mb-4">Tulis Komentar</h4>
                            <form action="{{ route('blog.comments.store', $article->id) }}" method="POST" class="flex flex-col gap-4">
                                @csrf
                                
                                @guest
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div class="flex flex-col gap-1.5">
                                            <label class="text-xs font-semibold text-slate-500">Nama Lengkap *</label>
                                            <input type="text" name="guest_name" required class="rounded-xl border border-slate-200 bg-white/50 px-3.5 py-2 text-sm dark:border-slate-800 dark:bg-slate-950/40 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                        </div>
                                        <div class="flex flex-col gap-1.5">
                                            <label class="text-xs font-semibold text-slate-500">Alamat Email *</label>
                                            <input type="email" name="guest_email" required class="rounded-xl border border-slate-200 bg-white/50 px-3.5 py-2 text-sm dark:border-slate-800 dark:bg-slate-950/40 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                        </div>
                                    </div>
                                @endguest

                                <div class="flex flex-col gap-1.5">
                                    <label class="text-xs font-semibold text-slate-500">Isi Komentar *</label>
                                    <textarea name="body" required rows="4" placeholder="Tulis tanggapan Anda di sini..." class="rounded-xl border border-slate-200 bg-white/50 px-3.5 py-2 text-sm dark:border-slate-800 dark:bg-slate-950/40 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"></textarea>
                                </div>

                                <button type="submit" class="self-end inline-flex items-center justify-center rounded-xl bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white shadow-md hover:bg-indigo-500 transition-colors">
                                    Kirim Komentar
                                </button>
                            </form>
                        </div>
                    </div>
                @endif

            </article>

            <!-- Sidebar Table of Contents (Col span 3) - Desktop Sticky -->
            <aside 
                class="hidden lg:block lg:col-span-3 sticky top-24 max-h-[calc(100vh-120px)] overflow-y-auto pl-4 transition-all duration-500"
                x-show="!focusMode"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-x-4"
                x-transition:enter-end="opacity-100 translate-x-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-x-0"
                x-transition:leave-end="opacity-0 translate-x-4"
            >
                @if(count($toc) > 0)
                    <div class="rounded-3xl border border-slate-200/60 bg-white/70 p-6 shadow-sm dark:border-slate-800/80 dark:bg-slate-900/40 backdrop-blur-sm mb-6">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-list-ol text-indigo-500"></i> Daftar Isi
                        </h3>
                        <nav class="flex flex-col gap-2">
                            @foreach($toc as $item)
                                <a href="#{{ $item['anchor'] }}" 
                                   class="text-xs text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-all border-l-2 pl-3 border-slate-200 dark:border-slate-800 py-1 line-clamp-2 {{ $item['level'] === 3 ? 'ml-3 text-[11px]' : 'font-medium' }}"
                                   data-toc-anchor="{{ $item['anchor'] }}">
                                     {{ $item['text'] }}
                                </a>
                            @endforeach
                        </nav>
                    </div>
                @endif

                <!-- Recommended Articles Widget -->
                @if($relatedArticles->count() > 0)
                    <div class="rounded-3xl border border-slate-200/60 bg-white/70 p-6 shadow-sm dark:border-slate-800/80 dark:bg-slate-900/40 backdrop-blur-sm">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-compass text-indigo-500"></i> Artikel Rekomendasi
                        </h3>
                        <div class="flex flex-col gap-5">
                            @foreach($relatedArticles as $related)
                                <a href="{{ route('blog.show', $related->slug) }}" class="group flex flex-col gap-2">
                                    <div class="relative aspect-[16/9] w-full rounded-2xl overflow-hidden bg-slate-100 dark:bg-slate-800 shadow-sm border border-slate-100 dark:border-slate-800/80">
                                        <img src="{{ $related->thumbnail_url }}" alt="{{ $related->title }}" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" loading="lazy">
                                    </div>
                                    <div class="flex flex-col gap-1 min-w-0">
                                        <div class="flex items-center gap-2 text-[10px] text-slate-400 dark:text-slate-500 font-medium">
                                            <span class="text-indigo-600 dark:text-indigo-400 font-bold uppercase tracking-wider">{{ $related->category->name }}</span>
                                            <span>&bull;</span>
                                            <span>{{ $related->reading_time_text }}</span>
                                        </div>
                                        <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors line-clamp-2 leading-snug">
                                            {{ $related->title }}
                                        </h4>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </aside>

        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function initBlogCodeBlocks() {
        const contentDiv = document.getElementById('blog-content');
        if (!contentDiv) return;

        // ── 1. Transform Quill code blocks into premium styled blocks ──────
        // Support both pre.ql-syntax (standard Quill pre) and div.ql-code-block-container (Quill 2.x div wrapper)
        const quillCodeContainers = contentDiv.querySelectorAll('pre.ql-syntax, div.ql-code-block-container');

        /**
         * Map detected hljs language names to human-readable labels.
         */
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
                console.error('Error formatting code container in blog:', e);
                // Gracefully unhide raw container on crash
                codeContainer.style.display = 'block';
            }
        });

        // Also handle regular <pre><code> blocks not from Quill (non ql-syntax)
        const regularPres = Array.from(contentDiv.querySelectorAll('pre')).filter(pre => {
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
                console.error('Error formatting regular pre block in blog:', e);
            }
        });

        // ── 2. Dynamic Anchors for TOC ─────────────────────────────────────
        const headings = contentDiv.querySelectorAll('h2, h3');
        headings.forEach(heading => {
            const text = heading.textContent;
            const slug = text.toLowerCase()
                             .replace(/[^a-z0-9\s-]/g, '')
                             .replace(/\s+/g, '-')
                             .replace(/-+/g, '-');
            heading.setAttribute('id', slug);
        });

        // ── 3. ScrollSpy for TOC highlight ────────────────────────────────
        const anchors = document.querySelectorAll('[data-toc-anchor]');
        if (anchors.length > 0) {
            const headingElements = Array.from(anchors)
                .map(a => document.getElementById(a.getAttribute('data-toc-anchor')))
                .filter(Boolean);

            function onScroll() {
                const scrollPos = window.scrollY + 100;
                let activeId = '';
                headingElements.forEach(heading => {
                    if (heading.offsetTop <= scrollPos) activeId = heading.getAttribute('id');
                });
                anchors.forEach(anchor => {
                    const isActive = anchor.getAttribute('data-toc-anchor') === activeId;
                    anchor.classList.toggle('border-indigo-500', isActive);
                    anchor.classList.toggle('text-indigo-600', isActive);
                    anchor.classList.toggle('dark:text-indigo-400', isActive);
                    anchor.classList.toggle('border-slate-200', !isActive);
                    anchor.classList.toggle('dark:border-slate-800', !isActive);
                    anchor.classList.toggle('text-slate-500', !isActive);
                });
            }
            window.addEventListener('scroll', onScroll);
            onScroll();
        }
    }

    // Initialize with safe check for document loading state to prevent timing issues
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initBlogCodeBlocks);
    } else {
        initBlogCodeBlocks();
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
