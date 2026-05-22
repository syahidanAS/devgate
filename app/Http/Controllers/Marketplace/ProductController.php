<?php

namespace App\Http\Controllers\Marketplace;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display product catalog with filters.
     */
    public function index(Request $request)
    {
        $query = Product::active()->with(['category']);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->input('category'));
            });
        }

        // Brand filter
        if ($request->filled('brand')) {
            $query->where('brand', $request->input('brand'));
        }

        // Price range filters
        if ($request->filled('min_price')) {
            $query->where('price', '>=', (int) $request->input('min_price'));
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', (int) $request->input('max_price'));
        }

        // Stock status filter
        if ($request->boolean('in_stock')) {
            $query->inStock();
        }

        // Sorting
        $sort = $request->input('sort', 'latest');
        if ($sort === 'price_asc') {
            $query->orderBy('price', 'asc');
        } elseif ($sort === 'price_desc') {
            $query->orderBy('price', 'desc');
        } else {
            $query->latest();
        }

        $products = $query->paginate(12)->withQueryString();

        $categories = ProductCategory::active()->get();
        // Unique brands in database
        $brands = Product::active()->whereNotNull('brand')->distinct()->pluck('brand');

        return view('marketplace.index', compact('products', 'categories', 'brands'));
    }

    /**
     * Display single product page.
     */
    public function show(string $slug)
    {
        $product = Product::where('slug', $slug)
            ->active()
            ->with(['category', 'relatedArticles.category', 'relatedArticles.author'])
            ->firstOrFail();

        // Increment view count
        $product->increment('view_count');

        // Related products in the same category
        $relatedProducts = Product::active()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->latest()
            ->take(4)
            ->get();

        return view('marketplace.show', compact('product', 'relatedProducts'));
    }
}
