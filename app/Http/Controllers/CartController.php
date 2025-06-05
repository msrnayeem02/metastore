<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class CartController extends Controller
{
    public function index()
    {
        // Retrieve cart data from session
        $cart = session()->get('cart', []);
        return view('frontend.cart.cart', compact('cart'));
    }

    public function addToCart(Request $request, $id)
    {
        try {
            $product = Product::findOrFail($id);
            $cart = session()->get('cart', []);

            if (isset($cart[$id])) {
                $cart[$id]['quantity']++;
            } else {
                $cart[$id] = [
                    "id" => $id, // Add this line
                    "title" => $product->title,
                    "quantity" => 1,
                    "price" => $product->price,
                    "image" => $product->image
                ];
            }

            session()->put('cart', $cart);

            return response()->json([
                'success' => true,
                'message' => 'Product added to cart successfully!',
                'totalQuantity' => array_sum(array_column($cart, 'quantity'))
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error adding product to cart'
            ], 500);
        }
    }

    public function updateCart(Request $request, $id)
    {
        try {
            $quantity = $request->input('quantity', 1);
            $cart = session()->get('cart', []);

            if (isset($cart[$id])) {
                $cart[$id]['quantity'] = $quantity;
                session()->put('cart', $cart);

                $subtotal = number_format($cart[$id]['price'] * $quantity, 2);
                $totalQuantity = array_sum(array_column($cart, 'quantity'));
                $totalPrice = number_format(array_sum(array_map(fn($item) => $item['price'] * $item['quantity'], $cart)), 2);

                return response()->json([
                    'success' => true,
                    'subtotal' => $subtotal,
                    'totalQuantity' => $totalQuantity,
                    'totalPrice' => $totalPrice
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Product not found in cart'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating cart'
            ], 500);
        }
    }

    public function orderNow(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            $cart[$id]['quantity'] += 1;
        } else {
            $cart[$id] = [
                'id' => $product->id,
                'title' => $product->title,
                'price' => $product->price,
                'quantity' => 1,
                'image' => $product->image ?? asset('frontend-assets/img/default.png'),
            ];
        }

        session()->put('cart', $cart);

        return redirect()->route('cart')
            ->with('success', 'Product added to cart and redirected to cart page.');
    }

    public function removeFromCart(Request $request, $id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }

        return redirect()->route('cart')
            ->with('success', 'Product removed from cart.');
    }

    public function buyNow(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $buyNowCart = [
            $id => [
                'id' => $product->id,
                'title' => $product->title,
                'price' => $product->price,
                'quantity' => 1,
                'image' => $product->image ?? asset('frontend-assets/img/default.png'),
            ]
        ];

        session()->put('buy_now_cart', $buyNowCart); // ✅ নতুন Session শুধু Buy Now এর জন্য

        return response()->json([
            'success' => true,
            'message' => 'Product ready for direct checkout!',
        ]);
    }
}
