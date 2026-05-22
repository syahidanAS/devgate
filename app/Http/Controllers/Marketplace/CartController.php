<?php

namespace App\Http\Controllers\Marketplace;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\Marketplace\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Display current cart items.
     */
    public function index()
    {
        $cartItems = $this->cartService->getItems();
        $summary = $this->cartService->getSummary();

        return view('marketplace.cart', compact('cartItems', 'summary'));
    }

    /**
     * Add an item to the shopping cart.
     */
    public function add(Request $request, Product $product)
    {
        $quantity = $request->integer('quantity', 1);
        if ($quantity <= 0) {
            $quantity = 1;
        }

        if (!$product->is_available) {
            return back()->with('error', 'Produk tidak tersedia atau kehabisan stok.');
        }

        $this->cartService->add($product, $quantity);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil ditambahkan ke keranjang.',
                'summary' => $this->cartService->getSummary(),
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Produk berhasil ditambahkan ke keranjang.');
    }

    /**
     * Update quantity of a cart item.
     */
    public function update(Request $request, int $id)
    {
        $quantity = $request->integer('quantity');

        $updated = $this->cartService->update($id, $quantity);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => $updated,
                'message' => $updated ? 'Keranjang berhasil diperbarui.' : 'Gagal memperbarui keranjang.',
                'summary' => $this->cartService->getSummary(),
            ]);
        }

        return back()->with($updated ? 'success' : 'error', $updated ? 'Keranjang berhasil diperbarui.' : 'Gagal memperbarui keranjang.');
    }

    /**
     * Remove an item from the cart.
     */
    public function remove(Request $request, int $id)
    {
        $removed = $this->cartService->remove($id);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => $removed,
                'message' => 'Produk dihapus dari keranjang.',
                'summary' => $this->cartService->getSummary(),
            ]);
        }

        return back()->with('success', 'Produk dihapus dari keranjang.');
    }
}
