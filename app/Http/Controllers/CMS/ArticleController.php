<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\ArticleTag;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    /**
     * Display a listing of articles.
     */
    public function index()
    {
        $user = Auth::user();
        
        $query = Article::with(['category', 'author']);

        // Authors can only see and manage their own articles
        if ($user->hasRole('author') && !$user->hasRole('superadmin')) {
            $query->where('user_id', $user->id);
        }

        $articles = $query->latest()->paginate(10);

        return view('cms.articles.index', compact('articles'));
    }

    /**
     * Show the form for creating a new article.
     */
    public function create()
    {
        $categories = ArticleCategory::active()->get();
        $tags = ArticleTag::all();
        $products = Product::active()->get();

        return view('cms.articles.create', compact('categories', 'tags', 'products'));
    }

    /**
     * Store a newly created article.
     */
    public function store(Request $request)
    {

        $validated = $request->validate([
            'title'            => 'required|string|max:200|unique:articles,title',
            'category_id'      => 'required|exists:article_categories,id',
            'excerpt'          => 'nullable|string|max:500',
            'body'             => 'required|string',
            'status'           => 'required|in:draft,published,scheduled',
            'scheduled_at'     => 'nullable|required_if:status,scheduled|date|after:now',
            'allow_comments'   => 'nullable|boolean',
            'is_featured'      => 'nullable|boolean',
            'meta_title'       => 'nullable|string|max:200',
            'meta_description' => 'nullable|string|max:500',
            'tags'             => 'nullable|array',
            'tags.*'           => 'exists:article_tags,id',
            'products'         => 'nullable|array',
            'products.*'       => 'exists:products,id',
            'thumbnail'        => 'nullable|image|max:2048', // max 2MB
        ]);

        $article = new Article();
        $article->user_id = Auth::id();
        $article->category_id = $validated['category_id'];
        $article->title = $validated['title'];
        $article->slug = Str::slug($validated['title']);
        $article->excerpt = $validated['excerpt'];
        $article->body = $validated['body'];
        $article->status = $validated['status'];
        $article->allow_comments = $request->boolean('allow_comments', true);
        $article->is_featured = $request->boolean('is_featured', false);
        $article->meta_title = $validated['meta_title'];
        $article->meta_description = $validated['meta_description'];

        if ($validated['status'] === 'scheduled') {
            $article->scheduled_at = $validated['scheduled_at'];
            $article->published_at = null;
        } elseif ($validated['status'] === 'published') {
            $article->published_at = now();
            $article->scheduled_at = null;
        }

        if ($request->hasFile('thumbnail')) {
            $path = $request->file('thumbnail')->store('thumbnails', 's3');
            $article->thumbnail = $path;
        }

        $article->save();

        // Sync tags & related products
        if (!empty($validated['tags'])) {
            $article->tags()->sync($validated['tags']);
        }
        
        if (!empty($validated['products'])) {
            $syncData = [];
            foreach ($validated['products'] as $index => $prodId) {
                $syncData[$prodId] = [
                    'context' => 'used_in_article',
                    'sort_order' => $index
                ];
            }
            $article->relatedProducts()->sync($syncData);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Artikel berhasil disimpan!',
                'redirect' => route('cms.articles.index')
            ]);
        }

        return redirect()->route('cms.articles.index')->with('success', 'Artikel berhasil disimpan!');
    }

    /**
     * Show edit article form.
     */
    public function edit(Article $article)
    {
        // Author authorization check
        $this->authorizeOwner($article);

        $categories = ArticleCategory::active()->get();
        $tags = ArticleTag::all();
        $products = Product::active()->get();

        return view('cms.articles.edit', compact('article', 'categories', 'tags', 'products'));
    }

    /**
     * Update specified article.
     */
    public function update(Request $request, Article $article)
    {
        $this->authorizeOwner($article);

        $validated = $request->validate([
            'title'            => 'required|string|max:200|unique:articles,title,' . $article->id,
            'category_id'      => 'required|exists:article_categories,id',
            'excerpt'          => 'nullable|string|max:500',
            'body'             => 'required|string',
            'status'           => 'required|in:draft,published,scheduled',
            'scheduled_at'     => 'nullable|required_if:status,scheduled|date|after:now',
            'allow_comments'   => 'nullable|boolean',
            'is_featured'      => 'nullable|boolean',
            'meta_title'       => 'nullable|string|max:200',
            'meta_description' => 'nullable|string|max:500',
            'tags'             => 'nullable|array',
            'tags.*'           => 'exists:article_tags,id',
            'products'         => 'nullable|array',
            'products.*'       => 'exists:products,id',
            'thumbnail'        => 'nullable|image|max:2048',
        ]);

        $article->category_id = $validated['category_id'];
        $article->title = $validated['title'];
        $article->slug = Str::slug($validated['title']);
        $article->excerpt = $validated['excerpt'];
        $article->body = $validated['body'];
        $article->status = $validated['status'];
        $article->allow_comments = $request->boolean('allow_comments', true);
        $article->is_featured = $request->boolean('is_featured', false);
        $article->meta_title = $validated['meta_title'];
        $article->meta_description = $validated['meta_description'];

        if ($validated['status'] === 'scheduled') {
            $article->scheduled_at = $validated['scheduled_at'];
            $article->published_at = null;
        } elseif ($validated['status'] === 'published') {
            if (!$article->published_at) {
                $article->published_at = now();
            }
            $article->scheduled_at = null;
        } else {
            $article->published_at = null;
            $article->scheduled_at = null;
        }

        if ($request->hasFile('thumbnail')) {
            $path = $request->file('thumbnail')->store('thumbnails', 's3');
            $article->thumbnail = $path;
        }

        $article->save();

        // Sync tags & related products
        $article->tags()->sync($validated['tags'] ?? []);
        
        $syncData = [];
        $selectedProducts = $validated['products'] ?? [];
        foreach ($selectedProducts as $index => $prodId) {
            $syncData[$prodId] = [
                'context' => 'used_in_article',
                'sort_order' => $index
            ];
        }
        $article->relatedProducts()->sync($syncData);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Artikel berhasil diperbarui!',
                'redirect' => route('cms.articles.index')
            ]);
        }

        return redirect()->route('cms.articles.index')->with('success', 'Artikel berhasil diperbarui!');
    }

    /**
     * Delete article.
     */
    public function destroy(Article $article)
    {
        $this->authorizeOwner($article);

        $article->delete();

        return redirect()->route('cms.articles.index')->with('success', 'Artikel berhasil dihapus.');
    }

    /**
     * Enforce security ownership policy checks.
     */
    protected function authorizeOwner(Article $article): void
    {
        $user = Auth::user();
        if ($user->hasRole('superadmin')) {
            return;
        }

        if ($article->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengelola artikel ini.');
        }
    }
}
