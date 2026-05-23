<?php

namespace App\Services\Marketplace;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartService
{
    /**
     * Get unique session identifier for guest shopping cart.
     */
    protected function getSessionId(): string
    {
        if (!Session::has('cart_session_id')) {
            Session::put('cart_session_id', Session::getId());
        }
        return Session::get('cart_session_id');
    }

    /**
     * Retrieve all cart items.
     */
    public function getItems(): Collection
    {
        if (Auth::check()) {
            return Cart::where('user_id', Auth::id())->with(['product', 'variant'])->get();
        }

        return Cart::where('session_id', $this->getSessionId())->with(['product', 'variant'])->get();
    }

    /**
     * Add product to cart.
     */
    public function add(Product $product, int $quantity = 1, ?int $variantId = null): Cart
    {
        $userId = Auth::id();
        $sessionId = $userId ? null : $this->getSessionId();

        $variant = null;
        if ($variantId) {
            $variant = $product->variants()->find($variantId);
        }

        // Validate stock
        $effectiveStock = $product->stock;
        if ($variant) {
            $effectiveStock = $variant->stock;
        }

        if ($product->track_stock && $effectiveStock < $quantity) {
            $quantity = $effectiveStock;
        }

        $query = Cart::query()->where('product_id', $product->id);
        
        if ($variantId) {
            $query->where('product_variant_id', $variantId);
        } else {
            $query->whereNull('product_variant_id');
        }

        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            $query->where('session_id', $sessionId);
        }

        $cartItem = $query->first();

        if ($cartItem) {
            $newQuantity = $cartItem->quantity + $quantity;
            if ($product->track_stock && $effectiveStock < $newQuantity) {
                $newQuantity = $effectiveStock;
            }
            $cartItem->update(['quantity' => $newQuantity]);
        } else {
            $cartItem = Cart::create([
                'user_id'            => $userId,
                'session_id'         => $sessionId,
                'product_id'         => $product->id,
                'product_variant_id' => $variantId,
                'quantity'           => $quantity,
            ]);
        }

        return $cartItem;
    }

    /**
     * Update quantity of a cart item.
     */
    public function update(int $cartId, int $quantity): bool
    {
        $cartItem = Cart::find($cartId);
        if (!$cartItem) {
            return false;
        }

        // Security check
        if (Auth::check() && $cartItem->user_id !== Auth::id()) {
            return false;
        }
        if (!Auth::check() && $cartItem->session_id !== $this->getSessionId()) {
            return false;
        }

        $product = $cartItem->product;
        $variant = $cartItem->variant;
        $effectiveStock = $variant ? $variant->stock : $product->stock;

        if ($product->track_stock && $effectiveStock < $quantity) {
            $quantity = $effectiveStock;
        }

        if ($quantity <= 0) {
            return $cartItem->delete();
        }

        return $cartItem->update(['quantity' => $quantity]);
    }

    /**
     * Remove item from cart.
     */
    public function remove(int $cartId): bool
    {
        $cartItem = Cart::find($cartId);
        if (!$cartItem) {
            return false;
        }

        // Security check
        if (Auth::check() && $cartItem->user_id !== Auth::id()) {
            return false;
        }
        if (!Auth::check() && $cartItem->session_id !== $this->getSessionId()) {
            return false;
        }

        return $cartItem->delete();
    }

    /**
     * Clear all items from current cart.
     */
    public function clear(): void
    {
        if (Auth::check()) {
            Cart::where('user_id', Auth::id())->delete();
        } else {
            Cart::where('session_id', $this->getSessionId())->delete();
        }
    }

    /**
     * Get totals of cart: count, subtotal, total weight.
     */
    public function getSummary(): array
    {
        $items = $this->getItems();
        $subtotal = 0;
        $totalWeight = 0;
        $totalQuantity = 0;

        foreach ($items as $item) {
            $subtotal += $item->subtotal;
            $weight = $item->variant ? $item->variant->effective_weight : $item->product->weight;
            $totalWeight += ($weight * $item->quantity);
            $totalQuantity += $item->quantity;
        }

        return [
            'items_count'    => $items->count(),
            'total_quantity' => $totalQuantity,
            'subtotal'       => $subtotal,
            'total_weight'   => $totalWeight, // in grams
        ];
    }

    /**
     * Merge guest cart into user database cart upon login.
     */
    public function mergeGuestCart(int $userId): void
    {
        $sessionId = $this->getSessionId();
        $guestItems = Cart::where('session_id', $sessionId)->get();

        foreach ($guestItems as $item) {
            // Check if user already has this product in cart
            $existing = Cart::where('user_id', $userId)
                ->where('product_id', $item->product_id)
                ->where('product_variant_id', $item->product_variant_id)
                ->first();

            if ($existing) {
                $newQty = $existing->quantity + $item->quantity;
                $effectiveStock = $item->variant ? $item->variant->stock : $item->product->stock;
                
                if ($item->product->track_stock && $effectiveStock < $newQty) {
                    $newQty = $effectiveStock;
                }
                $existing->update(['quantity' => $newQty]);
                $item->delete();
            } else {
                $item->update([
                    'user_id'    => $userId,
                    'session_id' => null,
                ]);
            }
        }
    }
}
