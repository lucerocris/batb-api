<?php

namespace App\Http\Controllers;

use App\Http\Resources\CartResource;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Display cart items.
     */
    public function index()
    {
        $cartItems = $this->getCartItems();
        $total = $cartItems->sum('subtotal');

        // OLD: Returns a Blade view (HTML)
        // return view('cart.index', compact('cartItems', 'total'));

        // NEW: Returns JSON data that React can consume
        return response()->json([
            'cartItems' => CartResource::collection($cartItems),
            'total' => $cartItems->sum(fn($item) => $item->quantity * $item->price)
        ]);
    }

    /**
     * Add item to cart.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'size' => 'nullable|string',
        ]);

        $product = Product::findOrFail($request->product_id);

        // Check if product is available
        if (!$product->is_active || $product->stock_status !== 'available') {
            return response()->json([
                'success' => false,
                'message' => 'Product is not available'
            ], 400);
        }

        $price = $product->sale_price ?? $product->base_price;

        // For authenticated users
        if (Auth::check()) {
            $cartItem = Cart::updateOrCreate(
                [
                    'user_id' => Auth::id(),
                    'product_id' => $request->product_id,
                    'size' => $request->size,
                ],
                [
                    'quantity' => $request->quantity,
                    'price' => $price,
                ]
            );
        } else {
            // For guest users
            $sessionId = session()->getId();

            $cartItem = Cart::updateOrCreate(
                [
                    'session_id' => $sessionId,
                    'product_id' => $request->product_id,
                    'size' => $request->size,
                ],
                [
                    'quantity' => $request->quantity,
                    'price' => $price,
                ]
            );
        }

        $cartCount = $this->getCartCount();
        $cartTotal = $this->getCartTotal();

        \Log::info('session id: ' . session()->getId());
        \Log::info('csrf_token(): ' . csrf_token());
        \Log::info('X-XSRF-TOKEN header: ' . request()->header('X-XSRF-TOKEN'));
        \Log::info('XSRF-TOKEN cookie: ' . request()->cookie('XSRF-TOKEN'));

        return response()->json([
            'success' => true,
            'message' => 'Product added to cart',
            'cart_count' => $cartCount,
            'cart_total' => $cartTotal,
            'item' => $cartItem->load('product')
        ]);
    }

    /**
     * Update cart item quantity.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cartItem = $this->getUserCart()->findOrFail($id);
        $cartItem->update(['quantity' => $request->quantity]);

        return response()->json([
            'success' => true,
            'message' => 'Cart updated',
            'subtotal' => $cartItem->subtotal,
            'cart_total' => $this->getCartTotal(),
        ]);
    }

    /**
     * Remove item from cart.
     */
    public function destroy($id)
    {
        $cartItem = $this->getUserCart()->findOrFail($id);
        $cartItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart',
            'cart_count' => $this->getCartCount(),
            'cart_total' => $this->getCartTotal(),
        ]);
    }

    /**
     * Clear entire cart.
     */
    public function clear()
    {
        $this->getUserCart()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cart cleared',
            'cart_count' => 0,
            'cart_total' => 0,
        ]);
    }

    /**
     * Get cart count (for badge).
     */
    public function count()
    {
        return response()->json([
            'count' => $this->getCartCount()
        ]);
    }

    // Helper methods
    private function getUserCart()
    {
        if (Auth::check()) {
            return Cart::where('user_id', Auth::id());
        }
        return Cart::where('session_id', session()->getId());
    }

    private function getCartItems()
    {
        return $this->getUserCart()->with('product')->get();
    }

    private function getCartCount()
    {
        return $this->getUserCart()->sum('quantity');
    }

    private function getCartTotal()
    {
        return $this->getCartItems()->sum('subtotal');
    }
}
