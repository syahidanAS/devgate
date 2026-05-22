<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of products.
     */
    public function index()
    {
        $products = Product::with('category')->latest()->paginate(10);
        return view('cms.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        $categories = ProductCategory::active()->get();
        return view('cms.products.create', compact('categories'));
    }

    /**
     * Store a newly created product.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:200|unique:products,name',
            'category_id'      => 'required|exists:product_categories,id',
            'sku'              => 'nullable|string|max:50|unique:products,sku',
            'brand'            => 'nullable|string|max:100',
            'price'            => 'required|integer|min:0',
            'sale_price'       => 'nullable|integer|min:0|lt:price',
            'stock'            => 'required|integer|min:0',
            'weight'           => 'required|integer|min:0',
            'excerpt'          => 'nullable|string|max:500',
            'description'      => 'required|string',
            'status'           => 'required|in:active,inactive,draft',
            'is_featured'      => 'nullable|boolean',
            'track_stock'      => 'nullable|boolean',
            'meta_title'       => 'nullable|string|max:200',
            'meta_description' => 'nullable|string|max:500',
            'specs_key'        => 'nullable|array',
            'specs_val'        => 'nullable|array',
            'images'           => 'nullable|array',
            'images.*'         => 'image|max:2048',
        ]);

        // Process specifications into JSON key-values
        $specifications = [];
        if (!empty($validated['specs_key'])) {
            foreach ($validated['specs_key'] as $i => $key) {
                if (!empty($key) && isset($validated['specs_val'][$i])) {
                    $specifications[$key] = $validated['specs_val'][$i];
                }
            }
        }

        $product = new Product();
        $product->user_id = Auth::id();
        $product->category_id = $validated['category_id'];
        $product->name = $validated['name'];
        $product->slug = Str::slug($validated['name']);
        $product->sku = $validated['sku'] ?: 'DG-' . strtoupper(Str::random(8));
        $product->brand = $validated['brand'];
        $product->price = $validated['price'];
        $product->sale_price = $validated['sale_price'];
        $product->stock = $validated['stock'];
        $product->weight = $validated['weight'];
        $product->excerpt = $validated['excerpt'];
        $product->description = $validated['description'];
        $product->status = $validated['status'];
        $product->is_featured = $request->boolean('is_featured', false);
        $product->track_stock = $request->boolean('track_stock', true);
        $product->specifications = $specifications;
        $product->meta_title = $validated['meta_title'];
        $product->meta_description = $validated['meta_description'];
        $product->save();

        // Handle multiple product gallery images using Spatie MediaLibrary
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $product->addMedia($image)->toMediaCollection('product-images');
            }
        }

        return redirect()->route('cms.products.index')->with('success', 'Produk berhasil ditambahkan!');
    }

    /**
     * Show form for editing product.
     */
    public function edit(Product $product)
    {
        $categories = ProductCategory::active()->get();
        return view('cms.products.edit', compact('product', 'categories'));
    }

    /**
     * Update specified product.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:200|unique:products,name,' . $product->id,
            'category_id'      => 'required|exists:product_categories,id',
            'sku'              => 'nullable|string|max:50|unique:products,sku,' . $product->id,
            'brand'            => 'nullable|string|max:100',
            'price'            => 'required|integer|min:0',
            'sale_price'       => 'nullable|integer|min:0|lt:price',
            'stock'            => 'required|integer|min:0',
            'weight'           => 'required|integer|min:0',
            'excerpt'          => 'nullable|string|max:500',
            'description'      => 'required|string',
            'status'           => 'required|in:active,inactive,draft',
            'is_featured'      => 'nullable|boolean',
            'track_stock'      => 'nullable|boolean',
            'meta_title'       => 'nullable|string|max:200',
            'meta_description' => 'nullable|string|max:500',
            'specs_key'        => 'nullable|array',
            'specs_val'        => 'nullable|array',
            'images'           => 'nullable|array',
            'images.*'         => 'image|max:2048',
        ]);

        $specifications = [];
        if (!empty($validated['specs_key'])) {
            foreach ($validated['specs_key'] as $i => $key) {
                if (!empty($key) && isset($validated['specs_val'][$i])) {
                    $specifications[$key] = $validated['specs_val'][$i];
                }
            }
        }

        $product->category_id = $validated['category_id'];
        $product->name = $validated['name'];
        $product->slug = Str::slug($validated['name']);
        $product->sku = $validated['sku'] ?: $product->sku;
        $product->brand = $validated['brand'];
        $product->price = $validated['price'];
        $product->sale_price = $validated['sale_price'];
        $product->stock = $validated['stock'];
        $product->weight = $validated['weight'];
        $product->excerpt = $validated['excerpt'];
        $product->description = $validated['description'];
        $product->status = $validated['status'];
        $product->is_featured = $request->boolean('is_featured', false);
        $product->track_stock = $request->boolean('track_stock', true);
        $product->specifications = $specifications;
        $product->meta_title = $validated['meta_title'];
        $product->meta_description = $validated['meta_description'];
        $product->save();

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $product->addMedia($image)->toMediaCollection('product-images');
            }
        }

        return redirect()->route('cms.products.index')->with('success', 'Produk berhasil diperbarui!');
    }

    /**
     * Delete product.
     */
    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('cms.products.index')->with('success', 'Produk berhasil dihapus.');
    }
}
