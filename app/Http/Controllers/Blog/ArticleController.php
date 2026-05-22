<?php

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\ArticleTag;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ArticleController extends Controller
{
    /**
     * Display technology blog homepage and articles catalog.
     */
    public function index(Request $request)
    {
        $query = Article::published()->with(['category', 'author', 'tags']);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('body', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->input('category'));
            });
        }

        // Tag filter
        if ($request->filled('tag')) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->where('slug', $request->input('tag'));
            });
        }

        $articles = $query->latest('published_at')->paginate(9)->withQueryString();

        // Get Featured & Trending articles
        $featuredArticles = Article::published()->featured()->latest('published_at')->take(3)->get();
        $trendingArticles = Article::published()->trending()->take(5)->get();

        $categories = ArticleCategory::active()->get();
        $tags = ArticleTag::has('articles')->take(20)->get();

        return view('blog.index', compact('articles', 'featuredArticles', 'trendingArticles', 'categories', 'tags'));
    }

    /**
     * Display a single detailed article.
     */
    public function show(string $slug, Request $request)
    {
        $article = Article::where('slug', $slug)
            ->published()
            ->with(['category', 'author', 'tags', 'approvedComments.replies.user', 'relatedProducts'])
            ->firstOrFail();

        // Increment view count with simple IP throttling to avoid page refresh abuse
        $ip = $request->ip();
        $cacheKey = 'viewed_article_' . $article->id . '_' . str_replace('.', '_', $ip);
        if (!Cache::has($cacheKey)) {
            $article->increment('view_count');
            Cache::put($cacheKey, true, now()->addHours(2));
        }

        // Get related articles in same category
        $relatedArticles = Article::published()
            ->where('category_id', $article->category_id)
            ->where('id', '!=', $article->id)
            ->latest('published_at')
            ->take(3)
            ->get();

        // Parse automatic Table of Contents from headings (h2, h3)
        $toc = $this->generateToc($article->body);

        return view('blog.show', compact('article', 'relatedArticles', 'toc'));
    }

    /**
     * Display articles under specific category.
     */
    public function category(string $slug)
    {
        $category = ArticleCategory::where('slug', $slug)->active()->firstOrFail();
        $articles = Article::published()
            ->where('category_id', $category->id)
            ->latest('published_at')
            ->paginate(9);

        return view('blog.category', compact('category', 'articles'));
    }

    /**
     * Display articles under specific tag.
     */
    public function tag(string $slug)
    {
        $tag = ArticleTag::where('slug', $slug)->firstOrFail();
        $articles = Article::published()
            ->whereHas('tags', function ($q) use ($tag) {
                $q->where('id', $tag->id);
            })
            ->latest('published_at')
            ->paginate(9);

        return view('blog.tag', compact('tag', 'articles'));
    }

    /**
     * Display author profile and their articles.
     */
    public function author(string $username)
    {
        $author = User::where('username', $username)->where('is_active', true)->firstOrFail();
        $articles = Article::published()
            ->where('user_id', $author->id)
            ->latest('published_at')
            ->paginate(9);

        return view('blog.author', compact('author', 'articles'));
    }

    /**
     * Helper to automatically parse h2 and h3 tags into an structured Table of Contents array.
     */
    protected function generateToc(string $html): array
    {
        $toc = [];
        preg_match_all('/<h([2-3])[^>]*>(.*?)<\/h\1>/i', $html, $matches, PREG_SET_ORDER);
        
        foreach ($matches as $match) {
            $level = (int) $match[1];
            $text = strip_tags($match[2]);
            $anchor = \Illuminate\Support\Str::slug($text);
            $toc[] = [
                'level' => $level,
                'text'  => $text,
                'anchor'=> $anchor,
            ];
        }

        return $toc;
    }
}
