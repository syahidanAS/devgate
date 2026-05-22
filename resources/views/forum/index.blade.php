@extends('layouts.app')

@section('title', 'Forum Diskusi — DevGate')

@section('content')
<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">

    {{-- ── Header ── --}}
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                <i class="fa-solid fa-comments text-indigo-500 mr-2 text-2xl"></i>
                Forum Diskusi
            </h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                Tanya, jawab, dan berbagi pengetahuan bersama komunitas DevGate.
            </p>
        </div>
        @auth
            <a href="{{ route('forum.create') }}"
               class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-indigo-600 to-violet-600 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-indigo-500/30 hover:shadow-indigo-500/50 hover:-translate-y-px transition-all">
                <i class="fa-solid fa-plus"></i> Buat Topik Baru
            </a>
        @else
            <a href="{{ route('login') }}"
               class="inline-flex items-center gap-2 rounded-xl border border-indigo-500/40 bg-indigo-500/10 px-5 py-2.5 text-sm font-semibold text-indigo-600 dark:text-indigo-400 hover:bg-indigo-500/20 transition-all">
                <i class="fa-solid fa-right-to-bracket"></i> Login untuk Buat Topik
            </a>
        @endauth
    </div>

    <div class="flex flex-col lg:flex-row gap-8">

        {{-- ── Main Content ── --}}
        <div class="flex-1 min-w-0">

            {{-- Stats bar --}}
            <div class="mb-5 grid grid-cols-3 gap-3">
                @foreach([
                    ['label' => 'Total Topik',  'value' => $stats['total'],   'color' => 'indigo', 'icon' => 'fa-layer-group'],
                    ['label' => 'Terjawab',     'value' => $stats['solved'],  'color' => 'emerald','icon' => 'fa-circle-check'],
                    ['label' => 'Belum Terjawab','value'=> $stats['unsolved'],'color' => 'amber',  'icon' => 'fa-circle-question'],
                ] as $s)
                <div class="rounded-xl border border-slate-200/60 dark:border-slate-800 bg-white/60 dark:bg-slate-900/60 backdrop-blur px-4 py-3 text-center">
                    <p class="text-xl font-extrabold text-{{ $s['color'] }}-600 dark:text-{{ $s['color'] }}-400">{{ $s['value'] }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ $s['label'] }}</p>
                </div>
                @endforeach
            </div>

            {{-- Filter + Search bar --}}
            <div class="mb-5 flex flex-col sm:flex-row gap-3">
                <div class="flex gap-2" id="forum-filters">
                    @foreach([
                        ['filter' => null,       'label' => 'Semua'],
                        ['filter' => 'unsolved', 'label' => 'Belum Terjawab'],
                        ['filter' => 'solved',   'label' => 'Terjawab'],
                    ] as $f)
                    <a href="{{ route('forum.index', array_filter(['filter' => $f['filter'], 'search' => request('search'), 'tag' => request('tag')])) }}"
                       data-filter="{{ $f['filter'] ?? '' }}"
                       class="filter-link rounded-lg px-3 py-1.5 text-xs font-semibold border transition-all
                              {{ request('filter') === $f['filter'] || (request('filter') === null && $f['filter'] === null)
                                  ? 'bg-indigo-600 text-white border-indigo-600 shadow-sm shadow-indigo-500/30 active-filter'
                                  : 'border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:border-indigo-400 dark:hover:border-indigo-500' }}">
                        {{ $f['label'] }}
                    </a>
                    @endforeach
                </div>
                <form id="forum-search-form" action="{{ route('forum.index') }}" method="GET" class="flex-1 relative">
                    @if(request('filter')) <input type="hidden" name="filter" value="{{ request('filter') }}" id="search-filter-input"> @endif
                    @if(request('tag'))    <input type="hidden" name="tag"    value="{{ request('tag') }}" id="search-tag-input"> @endif
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari topik..."
                           class="w-full rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 pl-9 pr-4 py-2 text-sm text-slate-700 dark:text-slate-200 placeholder-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 transition-all">
                </form>
            </div>

            {{-- Thread list container --}}
            <div id="thread-list-container" class="transition-opacity duration-250">
                @include('forum._thread_list')
            </div>
        </div>

        {{-- ── Sidebar ── --}}
        <div class="lg:w-64 xl:w-72 shrink-0 space-y-5">

            {{-- Popular Tags --}}
            <div class="rounded-2xl border border-slate-200/70 dark:border-slate-800 bg-white/70 dark:bg-slate-900/70 backdrop-blur p-5">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-3">
                    <i class="fa-solid fa-tags text-indigo-500 mr-1.5"></i> Tag Populer
                </h3>
                <div class="flex flex-wrap gap-2" id="sidebar-tags-container">
                    @forelse($popularTags as $tag => $count)
                        <a href="{{ route('forum.index', ['tag' => $tag]) }}"
                           data-tag="{{ $tag }}"
                           class="tag-link inline-flex items-center gap-1 rounded-lg border {{ request('tag') === $tag ? 'bg-indigo-600 text-white border-indigo-600 active-tag' : 'bg-slate-100 dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:border-indigo-400' }} px-2.5 py-1 text-xs font-semibold transition-all">
                            {{ $tag }}
                            <span class="text-[10px] opacity-60">{{ $count }}</span>
                        </a>
                    @empty
                        <p class="text-xs text-slate-400">Belum ada tag.</p>
                    @endforelse
                </div>
            </div>

            {{-- How to use --}}
            <div class="rounded-2xl border border-slate-200/70 dark:border-slate-800 bg-white/70 dark:bg-slate-900/70 backdrop-blur p-5">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-3">
                    <i class="fa-solid fa-circle-info text-cyan-500 mr-1.5"></i> Panduan Forum
                </h3>
                <ul class="space-y-2.5 text-xs text-slate-500 dark:text-slate-400">
                    <li class="flex items-start gap-2"><i class="fa-solid fa-check-circle text-emerald-500 mt-0.5 shrink-0"></i> Buat judul yang jelas dan spesifik</li>
                    <li class="flex items-start gap-2"><i class="fa-solid fa-check-circle text-emerald-500 mt-0.5 shrink-0"></i> Sertakan kode/error jika ada</li>
                    <li class="flex items-start gap-2"><i class="fa-solid fa-check-circle text-emerald-500 mt-0.5 shrink-0"></i> Tambahkan tag relevan</li>
                    <li class="flex items-start gap-2"><i class="fa-solid fa-check-circle text-emerald-500 mt-0.5 shrink-0"></i> Tandai jawaban terbaik jika masalah terselesaikan</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const threadContainer = document.getElementById('thread-list-container');
    const searchForm = document.getElementById('forum-search-form');
    const searchInput = searchForm ? searchForm.querySelector('input[name="search"]') : null;
    const filterContainer = document.getElementById('forum-filters');
    const sidebarTagsContainer = document.getElementById('sidebar-tags-container');

    // Keep track of our current active filter, tag, search and page
    let currentFilter = '{{ request('filter') }}';
    let currentTag = '{{ request('tag') }}';
    let currentSearch = '{{ request('search') }}';
    let currentPage = '{{ request('page', 1) }}';

    // A helper to construct URL and trigger the fetch
    function updateForum(params = {}) {
        // Update states
        if (params.hasOwnProperty('filter')) currentFilter = params.filter;
        if (params.hasOwnProperty('tag')) currentTag = params.tag;
        if (params.hasOwnProperty('search')) currentSearch = params.search;
        if (params.hasOwnProperty('page')) currentPage = params.page;

        // Build request URL
        const queryParams = new URLSearchParams();
        if (currentFilter) queryParams.set('filter', currentFilter);
        if (currentTag) queryParams.set('tag', currentTag);
        if (currentSearch) queryParams.set('search', currentSearch);
        if (currentPage && currentPage !== '1') queryParams.set('page', currentPage);

        const queryString = queryParams.toString();
        const displayUrl = queryString ? `{{ route('forum.index') }}?${queryString}` : `{{ route('forum.index') }}`;

        // Visual loading indicator (opacity)
        if (threadContainer) {
            threadContainer.style.opacity = '0.5';
            threadContainer.style.pointerEvents = 'none';
        }

        // Fetch AJAX
        fetch(displayUrl, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            // Update thread list
            if (threadContainer && data.html) {
                threadContainer.innerHTML = data.html;
                threadContainer.style.opacity = '1';
                threadContainer.style.pointerEvents = 'auto';
            }

            // Update browser URL
            history.pushState({ filter: currentFilter, tag: currentTag, search: currentSearch, page: currentPage }, '', displayUrl);

            // Update filter buttons classes
            updateFilterButtonsUI();

            // Update sidebar tags UI
            updateSidebarTagsUI();

            // Update hidden inputs in the search form
            updateSearchFormHiddenInputs();
        })
        .catch(error => {
            console.error('Error fetching threads:', error);
            if (threadContainer) {
                threadContainer.style.opacity = '1';
                threadContainer.style.pointerEvents = 'auto';
            }
        });
    }

    // Update the visual classes of the filter buttons
    function updateFilterButtonsUI() {
        if (!filterContainer) return;
        const filterLinks = filterContainer.querySelectorAll('.filter-link');
        filterLinks.forEach(link => {
            const f = link.getAttribute('data-filter') || '';
            if (f === currentFilter) {
                link.className = 'filter-link rounded-lg px-3 py-1.5 text-xs font-semibold border transition-all bg-indigo-600 text-white border-indigo-600 shadow-sm shadow-indigo-500/30 active-filter';
            } else {
                link.className = 'filter-link rounded-lg px-3 py-1.5 text-xs font-semibold border transition-all border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:border-indigo-400 dark:hover:border-indigo-500';
            }
        });
    }

    // Update the visual classes of the sidebar tags
    function updateSidebarTagsUI() {
        if (!sidebarTagsContainer) return;
        const tagLinks = sidebarTagsContainer.querySelectorAll('.tag-link');
        tagLinks.forEach(link => {
            const t = link.getAttribute('data-tag') || '';
            if (t === currentTag) {
                link.className = 'tag-link inline-flex items-center gap-1 rounded-lg border bg-indigo-600 text-white border-indigo-600 active-tag px-2.5 py-1 text-xs font-semibold transition-all';
            } else {
                link.className = 'tag-link inline-flex items-center gap-1 rounded-lg border bg-slate-100 dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:border-indigo-400 px-2.5 py-1 text-xs font-semibold transition-all';
            }
        });
    }

    // Update hidden fields in search form so that standard submit works if necessary
    function updateSearchFormHiddenInputs() {
        if (!searchForm) return;
        let filterInput = document.getElementById('search-filter-input');
        let tagInput = document.getElementById('search-tag-input');

        if (currentFilter) {
            if (!filterInput) {
                filterInput = document.createElement('input');
                filterInput.type = 'hidden';
                filterInput.name = 'filter';
                filterInput.id = 'search-filter-input';
                searchForm.appendChild(filterInput);
            }
            filterInput.value = currentFilter;
        } else if (filterInput) {
            filterInput.remove();
        }

        if (currentTag) {
            if (!tagInput) {
                tagInput = document.createElement('input');
                tagInput.type = 'hidden';
                tagInput.name = 'tag';
                tagInput.id = 'search-tag-input';
                searchForm.appendChild(tagInput);
            }
            tagInput.value = currentTag;
        } else if (tagInput) {
            tagInput.remove();
        }
    }

    // Event listener for filter links
    if (filterContainer) {
        filterContainer.addEventListener('click', function(e) {
            const link = e.target.closest('.filter-link');
            if (link) {
                e.preventDefault();
                const filter = link.getAttribute('data-filter') || '';
                updateForum({ filter: filter, page: 1 });
            }
        });
    }

    // Event listener for sidebar tag links
    if (sidebarTagsContainer) {
        sidebarTagsContainer.addEventListener('click', function(e) {
            const link = e.target.closest('.tag-link');
            if (link) {
                e.preventDefault();
                const tag = link.getAttribute('data-tag') || '';
                // Toggle behavior: if clicking already active tag, reset it
                const newTag = (tag === currentTag) ? '' : tag;
                updateForum({ tag: newTag, page: 1 });
            }
        });
    }

    // Event listener for elements inside the dynamic thread-list-container (pagination & active tag reset)
    if (threadContainer) {
        threadContainer.addEventListener('click', function(e) {
            // Check for pagination links
            const paginationLink = e.target.closest('nav a');
            if (paginationLink) {
                e.preventDefault();
                try {
                    const href = paginationLink.getAttribute('href');
                    if (href) {
                        const url = new URL(href);
                        const page = url.searchParams.get('page') || 1;
                        updateForum({ page: page });
                    }
                } catch (err) {
                    console.error('Pagination parsing error:', err);
                }
                return;
            }

            // Check for active tag reset (close button)
            const tagReset = e.target.closest('.tag-reset-link');
            if (tagReset) {
                e.preventDefault();
                updateForum({ tag: '', page: 1 });
                return;
            }
        });
    }

    // Debounced search input
    if (searchInput) {
        let debounceTimer;
        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                const searchVal = searchInput.value.trim();
                updateForum({ search: searchVal, page: 1 });
            }, 400);
        });

        // Prevent search form submit and trigger updateForum instead
        if (searchForm) {
            searchForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const searchVal = searchInput.value.trim();
                updateForum({ search: searchVal, page: 1 });
            });
        }
    }

    // Listen to history popstate (e.g. browser back/forward buttons)
    window.addEventListener('popstate', function(e) {
        if (e.state) {
            currentFilter = e.state.filter || '';
            currentTag = e.state.tag || '';
            currentSearch = e.state.search || '';
            currentPage = e.state.page || 1;

            if (searchInput) {
                searchInput.value = currentSearch;
            }

            // Fetch state from browser history
            const queryParams = new URLSearchParams();
            if (currentFilter) queryParams.set('filter', currentFilter);
            if (currentTag) queryParams.set('tag', currentTag);
            if (currentSearch) queryParams.set('search', currentSearch);
            if (currentPage && currentPage !== '1') queryParams.set('page', currentPage);

            const queryString = queryParams.toString();
            const displayUrl = `{{ route('forum.index') }}?${queryString}`;

            if (threadContainer) {
                threadContainer.style.opacity = '0.5';
            }

            fetch(displayUrl, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(data => {
                if (threadContainer && data.html) {
                    threadContainer.innerHTML = data.html;
                    threadContainer.style.opacity = '1';
                }
                updateFilterButtonsUI();
                updateSidebarTagsUI();
                updateSearchFormHiddenInputs();
            })
            .catch(() => {
                if (threadContainer) threadContainer.style.opacity = '1';
            });
        }
    });

    // Initialize history state on page load
    history.replaceState({ filter: currentFilter, tag: currentTag, search: currentSearch, page: currentPage }, '', window.location.href);
});
</script>
@endsection
