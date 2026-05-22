<?php

namespace App\Http\Controllers\Forum;

use App\Http\Controllers\Controller;
use App\Models\Thread;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ThreadController extends Controller
{
    /**
     * Forum index — list threads with filters.
     */
    public function index(Request $request)
    {
        $query = Thread::with('user')
            ->withCount('replies');

        // Filter: solved/unsolved
        if ($request->filter === 'solved') {
            $query->solved();
        } elseif ($request->filter === 'unsolved') {
            $query->unsolved();
        }

        // Filter: by tag
        if ($request->tag) {
            $tag = $request->tag;
            $query->whereJsonContains('tags', $tag);
        }

        // Search
        if ($request->search) {
            $q = '%' . $request->search . '%';
            $query->where(function ($q2) use ($q) {
                $q2->where('title', 'ilike', $q)
                   ->orWhere('body', 'ilike', $q);
            });
        }

        $threads = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

        // Popular tags sidebar
        $popularTags = Thread::whereNotNull('tags')
            ->get()
            ->flatMap(fn($t) => $t->tags ?? [])
            ->countBy()
            ->sortDesc()
            ->take(20);

        $stats = [
            'total'   => Thread::count(),
            'solved'  => Thread::solved()->count(),
            'unsolved'=> Thread::unsolved()->count(),
        ];

        if ($request->ajax()) {
            return response()->json([
                'html' => view('forum._thread_list', compact('threads'))->render(),
            ]);
        }

        return view('forum.index', compact('threads', 'popularTags', 'stats'));
    }

    /**
     * Show a single thread and its replies.
     */
    public function show(string $slug)
    {
        $thread = Thread::with([
            'user',
            // Only top-level replies (no parent); children are loaded recursively via the children() relationship
            'replies' => function ($q) {
                $q->whereNull('parent_id')
                  ->with(['user', 'likedByUsers', 'children'])
                  ->orderBy('is_solution', 'desc')
                  ->orderBy('created_at');
            },
        ])
            ->where('slug', $slug)
            ->firstOrFail();

        $thread->incrementViews();

        $likedReplyIds = collect();
        if (Auth::check()) {
            $likedReplyIds = $thread->replies
                ->filter(fn($r) => $r->isLikedBy(Auth::user()))
                ->pluck('id');
        }

        return view('forum.show', compact('thread', 'likedReplyIds'));
    }


    /**
     * Show form to create a new thread.
     */
    public function create()
    {
        return view('forum.create');
    }

    /**
     * Store a new thread.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'min:10', 'max:255'],
            'body'  => ['required', 'string', 'min:20'],
            'tags'  => ['nullable', 'string'],
        ]);

        // Parse comma-separated tags into array, max 5 tags
        $tags = null;
        if (!empty($validated['tags'])) {
            $tags = collect(explode(',', $validated['tags']))
                ->map(fn($t) => strtolower(trim($t)))
                ->filter()
                ->unique()
                ->take(5)
                ->values()
                ->all();
        }

        $thread = Thread::create([
            'user_id' => Auth::id(),
            'title'   => $validated['title'],
            'slug'    => Thread::generateSlug($validated['title']),
            'body'    => $validated['body'],
            'tags'    => $tags,
        ]);

        return redirect()->route('forum.show', $thread->slug)
            ->with('success', 'Topik berhasil dibuat!');
    }

    /**
     * Toggle the thread solved status.
     */
    public function toggleSolved(Thread $thread)
    {
        abort_unless(Auth::id() === $thread->user_id || Auth::user()->hasRole('superadmin'), 403);

        $thread->update([
            'is_solved' => !$thread->is_solved
        ]);

        $message = $thread->is_solved ? 'Diskusi berhasil ditandai sebagai Terjawab!' : 'Status diskusi diubah menjadi Belum Terjawab.';

        return back()->with('success', $message);
    }

    /**
     * Toggle the thread closed status.
     */
    public function toggleClosed(Thread $thread)
    {
        abort_unless(Auth::id() === $thread->user_id || Auth::user()->hasRole('superadmin'), 403);

        $thread->update([
            'is_closed' => !$thread->is_closed
        ]);

        $message = $thread->is_closed ? 'Diskusi berhasil Ditutup! Anggota tidak dapat mengirim balasan baru.' : 'Diskusi berhasil Dibuka kembali!';

        return back()->with('success', $message);
    }
}
